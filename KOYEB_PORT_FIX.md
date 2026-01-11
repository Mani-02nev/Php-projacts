# ⚠️ CRITICAL: Koyeb Port Configuration Issue

## 🔴 The Real Problem

Your logs show:
```
TCP health check failed on port 8000.
```

But your app runs on **port 80**. This means:

**Koyeb Dashboard UI is configured to check port 8000, NOT port 80!**

The `koyeb.yaml` file is being **IGNORED** by Koyeb. You MUST change the port in the Koyeb Dashboard UI.

---

## ✅ SOLUTION: Change Port in Koyeb Dashboard

### Step 1: Go to Koyeb Dashboard

1. Open [Koyeb Dashboard](https://app.koyeb.com)
2. Click on your app (likely named "6xpress" or similar)

### Step 2: Edit App Settings

Click on **"Settings"** or **"Edit"** button

### Step 3: Find Port Configuration

Look for one of these sections:
- **"Exposed Ports"**
- **"Port"**
- **"Service Port"**
- **"Health Check Port"**

### Step 4: Change Port from 8000 to 80

**Current (WRONG):**
```
Port: 8000  ❌
```

**Change to (CORRECT):**
```
Port: 80  ✅
```

### Step 5: Save and Redeploy

1. Click **"Save"** or **"Update"**
2. Click **"Redeploy"** or **"Deploy"**
3. Wait 2-3 minutes

---

## 📸 Visual Guide

When you're in the Koyeb dashboard, you should see something like:

```
┌─────────────────────────────────┐
│  Service Configuration          │
├─────────────────────────────────┤
│  Port: [8000]  ← CHANGE THIS!  │
│        ^^^^^^                    │
│  Change to: 80                   │
└─────────────────────────────────┘
```

---

## 🎯 Why This Happens

Koyeb has TWO ways to configure ports:

1. **koyeb.yaml file** (in your code) ← We configured this ✅
2. **Dashboard UI** (on Koyeb website) ← This overrides the YAML! ⚠️

**The Dashboard UI setting takes priority over koyeb.yaml!**

That's why it's still checking port 8000 even though we set port 80 in the YAML file.

---

## 🔍 How to Verify

After changing to port 80 and redeploying, check the logs:

### ✅ Success Looks Like:
```
Apache/2.4.65 (Debian) PHP/8.2.30 configured -- resuming normal operations
Instance is starting... Waiting for health checks to pass.
Health check passed on port 80  ← Should say 80, not 8000!
Instance is healthy
Deployment successful
```

### ❌ Still Failing Looks Like:
```
TCP health check failed on port 8000  ← Still wrong!
```

If you still see "port 8000", the dashboard setting wasn't changed.

---

## 📋 Complete Checklist

- [ ] Go to Koyeb Dashboard
- [ ] Click on your app
- [ ] Click "Settings" or "Edit"
- [ ] Find "Port" or "Exposed Ports" section
- [ ] Change from **8000** to **80**
- [ ] Click "Save"
- [ ] Click "Redeploy"
- [ ] Wait for deployment
- [ ] Check logs for "port 80" (not 8000)
- [ ] Verify app is accessible

---

## 🆘 If You Can't Find Port Setting

If you can't find where to change the port in the UI:

### Option 1: Delete and Recreate App

1. Delete the current app in Koyeb
2. Create a new app
3. When configuring, look for **"Port"** field
4. Enter **80** (not 8000)
5. Complete the setup

### Option 2: Use Koyeb CLI

```bash
# Install Koyeb CLI
brew install koyeb/tap/koyeb

# Login
koyeb login

# Update service port
koyeb service update <your-service-name> --port 80
```

### Option 3: Contact Koyeb Support

If nothing works:
- Go to Koyeb dashboard
- Click "Help" or "Support"
- Tell them: "My app runs on port 80 but health checks are on port 8000"

---

## 💡 Alternative: Change Apache to Port 8000

**NOT RECOMMENDED**, but if you absolutely can't change Koyeb's port setting, you could change Apache to run on port 8000 instead. But this is backwards - the platform should match your app, not vice versa.

---

## 🎉 After Successful Deployment

Once the port is correctly set to 80:

1. ✅ Health checks will pass
2. ✅ Instance will stay running
3. ✅ You'll get a live URL
4. ✅ Your 6Xpress app will be accessible!

---

## 📞 Need Help?

If you're stuck:
1. Take a screenshot of your Koyeb app settings page
2. Share it so I can see where the port configuration is
3. I'll guide you to the exact location

---

**The fix is simple: Change port from 8000 to 80 in Koyeb Dashboard UI!** 🎯
