<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);
$message = strtolower(trim($input['message'] ?? ''));
$state = $input['state'] ?? [
    'step' => 'init',
    'category' => null,
    'budget' => null,
    'usage' => null
];

$response = [
    'message' => "I didn't catch that. Could you rephrase?",
    'state' => $state,
    'products' => []
];

// Load Products (Simulated Database)
$all_products = read_csv(PRODUCTS_CSV); 

// --- NLP HELPER FUNCTIONS ---

function detect_category($text) {
    $map = [
        'mobile' => 'Electronics', 'phone' => 'Electronics', 'smartphone' => 'Electronics',
        'laptop' => 'Electronics', 'computer' => 'Electronics',
        'watch' => 'Electronics', 'smartwatch' => 'Electronics',
        'headphone' => 'Electronics', 'earbud' => 'Electronics', 'speaker' => 'Electronics',
        't-shirt' => 'Fashion', 'shirt' => 'Fashion', 'jeans' => 'Fashion', 'dress' => 'Fashion',
        'shoe' => 'Fashion', 'sneaker' => 'Fashion',
        'book' => 'Books', 'novel' => 'Books',
        'cream' => 'Beauty', 'makeup' => 'Beauty',
        'furniture' => 'Home & Living', 'sofa' => 'Home & Living', 'chair' => 'Home & Living'
    ];
    
    foreach ($map as $keyword => $category) {
        if (strpos($text, $keyword) !== false) {
            // Refine sub-type for filtering
            return ['main' => $category, 'keyword' => $keyword]; 
        }
    }
    return null;
}

function detect_budget($text) {
    // Check for explicit numbers
    if (preg_match('/(\d+)k/', $text, $matches)) {
        return (int)$matches[1] * 1000;
    }
    if (preg_match('/(\d+000)/', $text, $matches)) {
        return (int)$matches[1];
    }
    
    // Check for qualitative terms
    if (strpos($text, 'cheap') !== false || strpos($text, 'low') !== false || strpos($text, 'budget') !== false) return 'low'; // < 5000
    if (strpos($text, 'mid') !== false || strpos($text, 'average') !== false || strpos($text, 'decent') !== false) return 'mid'; // 5000 - 20000
    if (strpos($text, 'high') !== false || strpos($text, 'expensive') !== false || strpos($text, 'premium') !== false) return 'high'; // > 20000
    
    return null;
}

function get_price_range($budget_val) {
    if ($budget_val === 'low') return [0, 5000];
    if ($budget_val === 'mid') return [5000, 20000];
    if ($budget_val === 'high') return [20000, 500000];
    if (is_numeric($budget_val)) return [0, $budget_val]; // Up to that amount
    return [0, 1000000];
}

function filter_products($all, $cat_info, $budget_range, $usage_text) {
    $candidates = [];
    
    foreach ($all as $p) {
        // 1. Category Match
        // Loose match: category name OR product name contains keyword
        $cat_match = false;
        if ($cat_info) {
             if (strtolower($p['category']) == strtolower($cat_info['main'])) {
                 // Check precise keyword if available (e.g. 'watch' inside 'Electronics')
                 if (strpos(strtolower($p['name']), $cat_info['keyword']) !== false || 
                     strpos(strtolower($p['description']), $cat_info['keyword']) !== false) {
                     $cat_match = true;
                 }
             }
        } else {
            $cat_match = true; // Fallback
        }

        if (!$cat_match) continue;

        // 2. Price Match
        $price = (float)$p['price'];
        if ($price < $budget_range[0] || $price > $budget_range[1]) continue;

        // 3. Usage Scoring
        $score = 0;
        $usage_words = explode(' ', strtolower($usage_text));
        $prod_text = strtolower($p['name'] . ' ' . $p['description']);
        
        foreach ($usage_words as $w) {
            if (strlen($w) > 3 && strpos($prod_text, $w) !== false) {
                $score++;
            }
        }
        // Boost for 'Premium' in description if user didn't ask for cheap
        if (strpos($prod_text, 'premium') !== false) $score += 0.5;

        $p['match_score'] = $score;
        $p['reason'] = generate_reason($p, $usage_text);
        $candidates[] = $p;
    }

    // Sort by Score desc, then Price asc
    usort($candidates, function($a, $b) {
        if ($a['match_score'] == $b['match_score']) {
            return $a['price'] <=> $b['price']; // Cheaper tie-breaker
        }
        return $b['match_score'] <=> $a['match_score'];
    });

    return array_slice($candidates, 0, 2);
}

function generate_reason($p, $usage) {
    $name = $p['name'];
    $r = "Fits your budget perfectly.";
    if (strpos(strtolower($usage), 'gaming') !== false) $r = "Great performance for gaming.";
    if (strpos(strtolower($usage), 'office') !== false) $r = "Reliable choice for work.";
    if (strpos(strtolower($usage), 'fitness') !== false) $r = "Perfect companion for workouts.";
    
    // Add feature highlight
    if (strpos(strtolower($p['description']), 'battery') !== false) $r .= " Has excellent battery life.";
    if (strpos(strtolower($p['description']), 'premium') !== false) $r .= " Premium build quality.";
    
    return $r;
}


// --- MAIN LOGIC FLOW ---

// Step 1: Detect Intent Overrides (Reset)
if (strpos($message, 'reset') !== false || strpos($message, 'start over') !== false) {
    $state = ['step' => 'init', 'category' => null, 'budget' => null, 'usage' => null];
    $response['message'] = "Okay, let's start fresh! What are you looking for?";
    $response['state'] = $state;
    echo json_encode($response);
    exit;
}

// State Machine
switch ($state['step']) {
    case 'init':
        $cat = detect_category($message);
        if ($cat) {
            $state['category'] = $cat;
            $state['step'] = 'asking_budget';
            $response['message'] = "Great choice! A <b>{$cat['keyword']}</b>. <br> What's your budget range? (e.g., Low, Mid-range, High, or under ₹5000)";
        } else {
            // General chit-chat or failure
            if (strpos($message, 'hello') !== false || strpos($message, 'hi') !== false) {
                $response['message'] = "Hello! Tell me what product you're looking for (e.g., 'Smart Watch', 'Laptop').";
            } else {
                $response['message'] = "I specialize in finding products. Could you specify a category like 'Shoes', 'Mobiles', or 'Furniture'?";
            }
        }
        break;

    case 'asking_budget':
        $budget = detect_budget($message);
        if ($budget !== null) {
            $state['budget'] = $budget;
            $state['step'] = 'asking_usage';
            $response['message'] = "Got it. And what will you be primarily using it for? <br> (e.g., Gaming, Office work, Daily use, Gifts, Fitness)";
        } else {
            // Allow skipping/unknown
            $state['budget'] = 'mid'; // Default
            $state['step'] = 'asking_usage';
            $response['message'] = "I'll assume a flexible budget. What's your primary use case? (e.g., Gaming, Professional, Casual)";
        }
        break;

    case 'asking_usage':
    case 'recommending': // Allow refinement loop
        $state['usage'] = $message;
        
        // EXECUTE SEARCH
        $range = get_price_range($state['budget']);
        $results = filter_products($all_products, $state['category'], $range, $message);
        
        if (count($results) > 0) {
            $response['products'] = $results;
            
            // Construct Response
            $list_html = "<ul>";
            foreach ($results as $p) {
                $list_html .= "<li>{$p['name']} - ₹{$p['price']}</li>";
            }
            $list_html .= "</ul>";
            
            $response['message'] = "Based on your needs for <b>" . ($state['category']['keyword'] ?? 'products') . "</b> suited for <b>" . htmlspecialchars($message) . "</b>, I found these top picks:<br>" . $list_html . "<br>Check the panel on the right for details! 👉<br><br><b>I'm ready for a new search! What else are you looking for?</b>";
            
            // RESET STATE TO INIT
            $state = ['step' => 'init', 'category' => null, 'budget' => null, 'usage' => null];

        } else {
            $response['message'] = "Hmm, I couldn't find an exact match for that specific combination. but here are some popular options in that category. <br><br><b>I've reset my memory so you can try a new search!</b>";
            // Maybe fallback to just category matches?
            $fallback = filter_products($all_products, $state['category'], [0, 1000000], '');
            if (count($fallback) > 0) {
                $response['products'] = $fallback;
            }
            // RESET STATE TO INIT
            $state = ['step' => 'init', 'category' => null, 'budget' => null, 'usage' => null];
        }
        break;
}

$response['state'] = $state;
echo json_encode($response);
exit;
