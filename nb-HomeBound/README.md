# nb-HomeBound 🚀

**Version 1.0.0** - AI-Powered Daily Story Adventure Generator

Create daily turn-based story adventures with AI-generated content, images, and audio narration. Simple, customizable, and ready for distribution!

---

## 🎮 What It Does

HomeBound generates standalone HTML story pages where players:
- Navigate through **3 turns** per adventure
- Make **2 choices** per turn (one correct, one leads to failure)
- Experience AI-generated **story text, images, and audio**
- Track progress via **cookies** (last played day)
- Share/bookmark individual days

---

## ⚡ Quick Start

### 1. **Install**
- **Standalone**: Upload `nb-HomeBound` folder to your web server
- **WordPress**: Upload as plugin and activate

### 2. **Configure API Key**
- Get free API key from https://aistudio.google.com/app/apikey
- Open `admin.php` → Settings accordion
- Paste API key and save

### 3. **Customize Story Settings** (Optional)
Edit these fields to change the story theme:
- **Story Character**: Default is janitor in space
- **Story Theme**: Space adventure, fantasy quest, time travel, etc.
- **Story Goal**: What the character is trying to accomplish
- **Character Personality**: Sarcastic, brave, cautious, etc.
- **Story Tone**: Family-friendly, dark comedy, serious, etc.

### 4. **Create Adventures**
- Open `admin.php` → Create Day accordion
- Click **"Generate Complete Day"** for full AI generation
- OR fill fields manually and use **[AI Fill]** buttons for blanks
- Click **"Save Day"** to generate the HTML page

### 5. **Share**
- Days saved to `generated-days/day-1.html`, `day-2.html`, etc.
- Calendar at `generated-days/index.html`
- Direct link or embed in WordPress with `[homebound_calendar]`

---

## 🎨 Features

### AI Generation (All Included FREE!)
- **Text**: Gemini 2.5 Pro (2 requests/min)
- **Images**: Gemini 2.5 Flash Image "Nano Banana" (1,500/day)
- **Audio**: Google Cloud Text-to-Speech (1M chars/month)
- **Single API Key** for everything!

### Admin Interface
- Beautiful sci-fi themed control panel
- Generate **complete days** or **individual fields**
- Fill-in-the-blanks workflow (AI fills what you don't)
- Real-time status feedback
- No database required - generates static HTML

### Player Experience
- Gorgeous communications screen design
- Audio narration with play/pause controls
- Randomized correct answers (replayable)
- Cookie-based progress tracking
- Mobile-responsive
- Shareable day URLs

### Customization
- **Fully customizable story themes**
- Change character, theme, goal, personality
- Retains 3-turn gameplay structure
- Not locked into janitor storyline!

---

## 📂 File Structure

```
nb-HomeBound/
├── admin.php                  # Main admin interface
├── admin-ai.js                # AI generation JavaScript
├── api-get-config.php         # Config API endpoint
├── api-generate-field.php     # Single field generation
├── api-generate-day.php       # Full day generation
├── api-generate-image.php     # Image generation
├── api-generate-audio.php     # Audio generation
├── day-template.html          # Player page template
├── index-template.html        # Calendar template
├── config.json                # Settings storage
├── nb-homebound.php           # WordPress plugin wrapper
├── README.md                  # This file
└── generated-days/            # Output directory
    ├── index.html             # Mission calendar
    ├── day-1.html             # Day 1 adventure
    ├── day-2.html             # Day 2 adventure
    └── assets/                # Generated images & audio
        ├── turn_1_*.png
        ├── turn_1_*.mp3
        └── ...
```

---

## 🔌 WordPress Integration

### Shortcodes

**Display full calendar:**
```
[homebound_calendar]
```

**Display specific day:**
```
[homebound_day number="1"]
```

### Admin Page
WordPress admin → HomeBound → Opens admin console in new tab

---

## 🎯 Usage Workflows

### Full AI Generation (Fastest)
1. Open admin.php
2. Click "Generate Complete Day"
3. Wait ~30 seconds
4. Review and tweak if needed
5. Click "Save Day"

### Hybrid (Your Ideas + AI Execution)
1. Write your own Planet Name or story prompt
2. Click individual [AI Fill] buttons for fields you want generated
3. Edit any AI-generated content
4. Click "Save Day"

### Manual (No AI)
1. Fill in all fields manually
2. Add your own image URLs
3. Skip audio or add your own MP3 URLs
4. Click "Save Day"

---

## 🌐 API Configuration

### Getting Your Key
1. Go to https://aistudio.google.com/app/apikey
2. Click "Create API Key"
3. Copy the key

### What One Key Gives You
- **Gemini 2.5 Pro**: Story text generation
- **Gemini 2.5 Flash Image**: Nano Banana images (NEW!)
- **Google Cloud TTS**: Audio narration

All three services use the **same API key**!

### Free Tier Limits
- **Text**: 2 requests per minute (plenty for story creation)
- **Images**: 1,500 per day (50 complete days!)
- **Audio**: 1 million characters per month (~300 days)

---

## 📖 Story Customization Examples

### Fantasy Quest
```
Character: a young wizard apprentice who accidentally summoned a portal
Theme: magical fantasy adventure with humor
Goal: finding the spell to close the portal before monsters invade
Personality: nervous but determined, always second-guessing themselves
Tone: Family-friendly fantasy with light comedy
```

### Zombie Survival
```
Character: a pizza delivery driver during the zombie apocalypse
Theme: survival horror with dark humor
Goal: delivering the last pizza order while zombies take over
Personality: sarcastic and resourceful, takes nothing seriously
Tone: Dark comedy, occasionally scary but mostly funny
```

### Time Travel Detective
```
Character: a detective who can travel back 24 hours
Theme: mystery thriller with time paradoxes
Goal: solving a crime before it happens
Personality: analytical and obsessive, keeps detailed notes
Tone: Serious mystery with mind-bending twists
```

---

## 🚀 Distribution Ready

### As Standalone Tool
- No dependencies except PHP 7.4+
- Works on any web server
- Just upload and configure API key

### As WordPress Plugin
- Install via Plugins → Add New → Upload
- Shortcodes for easy embedding
- Works with any theme

### For Clients
- Rebrand the interface colors/text
- Customize story templates
- White-label friendly

---

## 🎨 Design Credits

Interface designs generated by Google Gemini:
- **Admin Console**: Dark sci-fi control panel theme
- **Player Screen**: Communication terminal design
- Colors: Orange (#E8772E), Teal Accent (#00A9A5)
- Font: Space Grotesk

---

## 🔧 Technical Details

### Requirements
- PHP 7.4 or higher
- cURL extension enabled
- Write permissions for `generated-days/` folder
- Google AI API key (free)

### Browser Support
- Modern browsers (Chrome, Firefox, Safari, Edge)
- Mobile responsive design
- Cookie support for progress tracking

### No Database
- All stories saved as static HTML files
- Configuration in JSON file
- Assets in `generated-days/assets/`
- Easy to backup/migrate

---

## 📝 Changelog

### Version 1.0.0 - 2025-11-14
- ✨ Initial release
- ✅ Customizable story parameters (character, theme, goal, tone)
- ✅ Complete AI generation (text, images, audio)
- ✅ Individual field AI generation
- ✅ Beautiful admin interface with accordions
- ✅ Player page with communication terminal design
- ✅ Calendar with cookie-based progress tracking
- ✅ WordPress plugin with shortcodes
- ✅ Static HTML output (no database)
- ✅ Single API key for all services

---

## 🤝 Support

Created by **Orange Jeff** for fun and distribution!

- Modify story themes without changing code
- Create unlimited adventures
- Share standalone HTML pages
- Rebrand and distribute as your own

---

## 📜 License

MIT License - Free to use, modify, and distribute!

---

## 🎮 Start Creating!

1. Get API key: https://aistudio.google.com/app/apikey
2. Open `admin.php` in browser
3. Configure settings
4. Click "Generate Complete Day"
5. Share `generated-days/index.html`

**That's it!** You're running a daily story adventure site! 🚀
