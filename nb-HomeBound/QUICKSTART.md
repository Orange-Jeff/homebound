# nb-HomeBound - Quick Start Guide

## 🚀 Get Running in 5 Minutes

### Step 1: Get Your Free API Key
1. Go to https://aistudio.google.com/app/apikey
2. Sign in with Google account
3. Click "Create API Key"
4. Copy the key (starts with "AIza...")

### Step 2: Configure HomeBound
1. Open your web browser
2. Navigate to: `http://localhost/nb-HomeBound/admin.php` (or your server URL)
3. Click "Story Settings" accordion to expand
4. Paste your API key in the first field
5. Click "Save Settings"

### Step 3: Create Your First Adventure
1. Scroll to "Create Day 1" accordion
2. Click the big **"Generate Complete Day"** button
3. Wait 20-30 seconds while AI creates the story
4. Review the generated content (edit if you want!)
5. Click **"💾 Save Day 1"**

### Step 4: View Your Story
1. Click the 🔗 icon in the top-right corner of admin page
2. OR navigate to: `http://localhost/nb-HomeBound/generated-days/index.html`
3. Click "Day 1" to play your adventure!

---

## 🎮 Playing a Story

1. Read the story paragraphs
2. Click the play button (▶) to hear audio narration
3. Choose Option A or Option B
4. Wrong choice = Mission Failed (try again!)
5. Right choice = Next turn appears
6. Complete all 3 turns = Success!

---

## ✨ Customizing Your Stories

### Change the Theme (Without Janitor in Space!)

1. Open admin.php → Story Settings
2. Edit these fields:

**Example: Fantasy Quest**
```
Story Character: a young wizard who accidentally opened a forbidden spellbook
Story Theme: magical fantasy adventure with whimsy
Story Goal: closing the portals before monsters invade the kingdom
Character Personality: curious but cautious, always learning from mistakes
Story Tone: Family-friendly fantasy with light humor
```

**Example: Detective Mystery**
```
Story Character: a detective investigating a series of strange disappearances
Story Theme: mystery thriller with supernatural elements
Story Goal: solving the case before the next victim disappears
Character Personality: sharp and intuitive, trusts their instincts
Story Tone: Serious mystery with occasional wit
```

3. Click "Save Settings"
4. Generate new days - they'll use your custom theme!

---

## 🛠️ Creating Stories Your Way

### Option 1: Full AI Generation (Fastest)
- Click "Generate Complete Day"
- Done in 30 seconds!

### Option 2: Guided AI (You Direct)
1. Type your own Planet Name (or location)
2. Click [AI Fill] next to individual fields
3. Edit any generated content
4. Mix your ideas with AI execution

### Option 3: Manual Creation (No AI)
- Fill in all fields yourself
- Add your own image URLs
- Add your own audio file URLs (MP3)
- Full control!

---

## 📁 File Locations

### Generated Stories
- **Calendar**: `generated-days/index.html`
- **Day Pages**: `generated-days/day-1.html`, `day-2.html`, etc.
- **Assets**: `generated-days/assets/` (images & audio)

### Configuration
- **Settings**: `config.json` (API key & story parameters)

### Admin
- **Control Panel**: `admin.php`

---

## 🔌 WordPress Usage

### Installation
1. Upload entire `nb-HomeBound` folder to `wp-content/plugins/`
2. Go to WordPress admin → Plugins
3. Find "nb-HomeBound" and click Activate

### Creating Stories
1. WordPress admin → HomeBound
2. Click "Open Admin Console"
3. Create days as normal

### Displaying Stories
**In any post or page:**

Show full calendar:
```
[homebound_calendar]
```

Show specific day:
```
[homebound_day number="1"]
```

---

## ⚠️ Troubleshooting

### "No API key configured"
- Make sure you saved settings after pasting API key
- Check config.json file exists and has your key

### AI generation fails
- Check your API key is valid at https://aistudio.google.com/
- You may have hit rate limits (2 requests/min for text)
- Wait a minute and try again

### Images don't appear
- Check `generated-days/assets/` folder exists and is writable
- Image generation has 1,500/day limit (very generous!)

### Audio doesn't play
- Check browser console for errors (F12)
- Make sure audio URLs are filled in
- Try clicking the play button instead of auto-play

### "Permission denied" errors
- Make sure `generated-days/` folder has write permissions
- On Linux: `chmod 755 generated-days` or `chmod 777 generated-days`

---

## 🎯 Tips & Tricks

### Creating Multiple Days Quickly
1. Generate Day 1
2. Save it
3. Immediately generate Day 2 (form resets)
4. Repeat!

### Reusing Good Content
- Copy/paste successful paragraphs as templates
- Edit planet names but keep story structure
- Build a library of good opening paragraphs

### Testing Stories
- Play through each day before sharing
- Check both choice A and B outcomes
- Listen to audio to catch any weird pronunciations

### Customizing Design
- Edit `day-template.html` to change player page colors/layout
- Edit `index-template.html` to change calendar design
- Tailwind CSS classes make it easy to customize

---

## 📊 Free Tier Limits

With one free Google AI API key:

- **Text Generation**: 2 requests per minute
  - 1 complete day = 3 requests (one per turn)
  - Can create ~20 days per hour

- **Image Generation**: 1,500 per day
  - 1 complete day = 3 images
  - Can create 500 complete days per day!

- **Audio Generation**: 1 million characters per month
  - Average day = ~3,000 characters
  - Can create 300+ days per month

**You won't hit these limits in normal use!**

---

## ✅ Checklist: Is It Working?

- [ ] Admin page loads at `admin.php`
- [ ] Settings save successfully
- [ ] "Generate Complete Day" button works
- [ ] All fields get populated with content
- [ ] Images appear in image URL fields
- [ ] Audio URLs appear (if audio enabled)
- [ ] Clicking "Save Day" creates `day-X.html` file
- [ ] Calendar page shows the new day
- [ ] Clicking day card loads the story
- [ ] Audio player appears and works
- [ ] Making choices advances turns
- [ ] Success/failure screens appear correctly

---

## 🆘 Need More Help?

Check the main README.md for:
- Full feature documentation
- API configuration details
- Story customization examples
- Technical requirements
- Distribution information

---

## 🎉 You're Ready!

Start creating awesome adventures! Remember:
1. Customize the story theme to match your vision
2. Generate or fill manually - your choice
3. Share the calendar URL or individual day links
4. Players' progress saved in cookies automatically

Have fun! 🚀
