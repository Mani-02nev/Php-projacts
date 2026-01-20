# 🔧 Koyeb Deployment Fix - Port Configuration Issue

## ❌ Problem Identified

Your deployment was failing with this error:
```
TCP health check failed on port 8000.
Instance stopped.
```

**Root Cause**: Koyeb was configured to check port **8000**, but Apache is running on port **80**.

## ✅ Solution Applied

I've fixed the configuration in two files:

### 1. Updated `Dockerfile`
- ✅ Added `ServerName localhost` to suppress Apache warnings
- ✅ Explicitly set Apache to listen on port 80
- ✅ Added `ENV PORT=80` environment variable
- ✅ Changed health check from HTTP to TCP (more reliable)

### 2. Updated `koyeb.yaml`
- ✅ Confirmed port is set to 80
- ✅ Changed health check to TCP on port 80
- ✅ Added 30-second grace period for health checks

## 🚀 Next Steps

### Step 1: Push the Fixed Code to GitHub

```bash
# Add the updated files
git add Dockerfile koyeb.yaml

# Commit the changes
git commit -m "Fix: Configure Apache to run on port 80 for Koyeb"

# Push to GitHub (replace 'origin' with your remote name if different)
git push origin main
```

### Step 2: Redeploy on Koyeb

**Option A: Automatic (if auto-deploy is enabled)**
- Koyeb will automatically detect the push and redeploy
- Wait 2-3 minutes for the new deployment

**Option B: Manual Redeploy**
1. Go to [Koyeb Dashboard](https://app.koyeb.com)
2. Click on your app
3. Click **"Redeploy"** or **"Deploy"**
4. Wait for the build to complete

### Step 3: Verify Port Configuration in Koyeb UI

While redeploying, double-check in Koyeb:
1. Go to your app settings
2. Find **"Exposed Ports"** or **"Port"** section
3. Ensure it shows: **Port 80**
4. If it shows 8000, change it to **80** and save

## 📊 What Changed

| File | Change | Why |
|------|--------|-----|
| `Dockerfile` | Added `ServerName localhost` | Fixes Apache warnings |
| `Dockerfile` | Added `ENV PORT=80` | Explicitly sets port |
| `koyeb.yaml` | Changed to TCP health check | More reliable than HTTP |
| `koyeb.yaml` | Added grace period | Gives Apache time to start |

## 🔍 How to Monitor the Deployment

Watch the Koyeb logs for these success indicators:

✅ **Good Signs:**
```
Apache/2.4.65 (Debian) PHP/8.2.30 configured -- resuming normal operations
Instance is healthy
Deployment successful
```

❌ **Bad Signs:**
```
TCP health check failed on port 8000  ← Wrong port!
Health check failed
Instance stopped
```

## 🎯 Expected Behavior After Fix

1. **Build Phase**: Docker image builds successfully
2. **Start Phase**: Apache starts on port 80
3. **Health Check**: TCP check on port 80 succeeds
4. **Running**: Instance stays healthy
5. **Live**: Your app is accessible at the Koyeb URL

## 🐛 If It Still Fails

### Check 1: Verify Port in Koyeb Dashboard
- Go to App → Settings → Ports
- Must be **80**, not 8000

### Check 2: View Deployment Logs
- Look for "listening on port" messages
- Check for any error messages

### Check 3: Test Locally with Docker
```bash
# Build the image
docker build -t 6xpress-test .

# Run on port 8080 locally (maps to 80 inside container)
docker run -p 8080:80 6xpress-test

# Test in browser
open http://localhost:8080
```

If it works locally but fails on Koyeb, the issue is in Koyeb configuration.

### Check 4: Koyeb Instance Size
- Nano instance might be too small
- Try upgrading to **Small** instance
- Go to Settings → Instance Type → Small

## 📝 Common Koyeb Port Issues

| Issue | Solution |
|-------|----------|
| Port mismatch | Ensure Koyeb port = Docker EXPOSE port |
| Health check timeout | Increase grace period to 60s |
| Instance too small | Upgrade to Small or Medium |
| Wrong protocol | Use TCP for basic checks, HTTP for advanced |

## 💡 Pro Tips

1. **Always use TCP health checks** for simple port availability
2. **Use HTTP health checks** only if you have a specific health endpoint
3. **Set grace period** to at least 30 seconds for PHP apps
4. **Monitor logs** during first deployment to catch issues early

## 🆘 Still Having Issues?

If the deployment still fails after these fixes:

1. **Share the latest logs** - Copy from Koyeb dashboard
2. **Check Koyeb status** - https://status.koyeb.com
3. **Try different region** - Some regions might have issues
4. **Contact Koyeb support** - They're very responsive

## ✅ Success Checklist

- [ ] Updated Dockerfile pushed to GitHub
- [ ] Updated koyeb.yaml pushed to GitHub
- [ ] Koyeb port setting is 80 (not 8000)
- [ ] Deployment logs show Apache starting
- [ ] Health checks passing
- [ ] App accessible at Koyeb URL

---

**The fix is ready! Push the changes and redeploy.** 🚀
