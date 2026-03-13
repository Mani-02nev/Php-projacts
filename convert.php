<?php
$dir = __DIR__;

function replaceInDir($dir)
{
    global $replacements;
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..' || $file === 'convert.php' || strpos($file, '.git') !== false) {
            continue;
        }

        $path = $dir . DIRECTORY_SEPARATOR . $file;
        if (is_dir($path)) {
            replaceInDir($path);
        }
        else {
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            if (in_array($ext, ['php', 'css', 'html', 'js'])) {
                $content = file_get_contents($path);
                $original = $content;

                // Specific sequential replacements
                $content = str_replace('bg-dark text-white', 'bg-white text-dark', $content);
                $content = str_replace('text-white bg-dark', 'text-dark bg-white', $content);
                $content = str_replace('navbar-dark', 'navbar-light', $content);

                // Regex to only replace hex codes safely without double replacing
                // We will collect all keys and do a preg_replace_callback
                $keys = array_keys($replacements);
                $escapedKeys = array_map(function ($k) {
                    return preg_quote($k, '/'); }, $keys);
                $pattern = '/(' . implode('|', $escapedKeys) . ')/i';

                $content = preg_replace_callback($pattern, function ($matches) use ($replacements) {
                    $key = strtoupper($matches[1]);
                    // check exact match first, then upper, then lower
                    if (isset($replacements[$matches[1]]))
                        return $replacements[$matches[1]];
                    if (isset($replacements[$key]))
                        return $replacements[$key];
                    $lowerKey = strtolower($matches[1]);
                    if (isset($replacements[$lowerKey]))
                        return $replacements[$lowerKey];
                    return $matches[1];
                }, $content);

                // Other simple strings
                $content = str_replace('data-bs-theme="dark"', 'data-bs-theme="light"', $content);
                $content = str_replace("setAttribute('data-bs-theme', 'dark')", "setAttribute('data-bs-theme', 'light')", $content);
                $content = str_replace("setItem('theme', 'dark')", "setItem('theme', 'light')", $content);
                $content = str_replace('enterprise-dark-theme.css', 'enterprise-light-theme.css', $content);

                if ($content !== $original) {
                    file_put_contents($path, $content);
                    echo "Updated: $path\n";
                }
            }
        }
    }
}

$replacements = [
    '#0B0B0E' => '#F8F9FA',
    '#0b0b0e' => '#F8F9FA',
    '#14161A' => '#FFFFFF',
    '#14161a' => '#FFFFFF',
    '#1A1D23' => '#F3F4F6',
    '#1a1d23' => '#F3F4F6',
    '#1F2937' => '#E5E7EB',
    '#1f2937' => '#E5E7EB',
    '#2D2D35' => '#E5E7EB',
    '#2d2d35' => '#E5E7EB',
    '#374151' => '#D1D5DB',
    '#374151' => '#D1D5DB',
    '#9CA3AF' => '#6B7280',
    '#9ca3af' => '#6B7280',
    '#E5E7EB' => '#374151',
    '#e5e7eb' => '#374151',
    '#F3F4F6' => '#1F2937',
    '#f3f4f6' => '#1F2937',
    'bg-dark' => 'bg-white',
    'border-secondary' => 'border-light-subtle',
    'border-dark' => 'border-light',
    'text-white' => 'text-body',
    'text-light' => 'text-muted',
    'var(--dark-bg-elevated)' => 'var(--light-bg-elevated)',
    'var(--dark-border-primary)' => 'var(--light-border-primary)',
    'rgba(255,255,255,0.05)' => 'rgba(0,0,0,0.05)',
    'rgba(255, 255, 255, 0.05)' => 'rgba(0, 0, 0, 0.05)',
    'rgba(255,255,255,0.2)' => 'rgba(0,0,0,0.1)',
    'rgba(255, 255, 255, 0.2)' => 'rgba(0, 0, 0, 0.1)'
];

replaceInDir($dir);

if (file_exists($dir . '/css/enterprise-dark-theme.css')) {
    rename($dir . '/css/enterprise-dark-theme.css', $dir . '/css/enterprise-light-theme.css');
    echo "Renamed enterprise-dark-theme.css to enterprise-light-theme.css\n";
}

echo "Conversion Complete!\n";
?>
