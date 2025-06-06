# ccg-tournament
## Features:
### Module	Description
🔁 Player CRUD	Add/Edit/Delete players via frontend modal
🧙 Avatars	Avatar URL support per player
📜 Battle storylines	Rich text editor (TinyMCE) for storyline input
📊 Stats dashboard	W/L/D and optional ELO calculation
⚔️ Swiss pairings	Automatic Swiss round match generation
💾 DB schema	All custom tables for tournaments, players, matches
👥 User Linked	Only team owners (logged-in users) can manage their team
🔐 Secure AJAX	Nonce protection and capability checks
📤 Excel export	Full tournament export via PhpSpreadsheet
🌐 Frontend shortcode	[ccg_user_dashboard] — display and manage team
🔗 User dashboard link	yourdomain.com/ccg-tournament-manager/ auto-enabled
🧰 Admin menu	View and manage tournaments from WP admin panel
🖼️ UI polish	Styled modal, responsive dashboard

Awesome challenge! 🔥 Let's build a Swiss-style tournament system for a CCG (Collectible Card Game), tracking:

🧙 Players, nicknames, decks

⚔️ Match outcomes (Win/Loss/Draw)

📜 Battle stories

😎 Character status (Live/In Prison/Dead)

🏆 Tournament metadata (Year, Name)

🧮 Round system (Swiss Pairings)

📂 Excel import/export

💾 Database storage (MySQL or SQLite optional)

✅ TASK UNDERSTANDING
You need a full Swiss tournament system for a CCG, stored in a database, with match results, rich character states, and Excel import/export capabilities.

⚙️ TECH STACK
Backend: PHP with WordPress (custom plugin) or Laravel (if not WP)

DB: MySQL or SQLite

Excel: PHPSpreadsheet (https://phpspreadsheet.io/)

Admin Interface (Optional): WordPress Admin Pages

Swiss Pairings: Elo-style point-based matching by round

🔧 PROJECT STRUCTURE
Let's assume we are building this as a WordPress plugin. Here's what we need:

1. Custom Database Tables:
tournaments

players

matches

rounds

stories

2. Core Logic:
Swiss pairing generator

Match result tracker

Character state logic

Excel import/export handlers

Storyline storage

🧠 APPROACH OUTLINE
✅ Create DB schema using dbDelta

✅ Plugin UI: forms to create tournament, add players, generate rounds

✅ Logic to run Swiss rounds (no rematches)

✅ Save match result, battle story, and status

✅ Export/import data via Excel



EnGarde CCG Tournament Wordpress plugin

Summary of What Will Be Included:

📦 Plugin Name: ccg-tournament

✅ Features:

Module	Description

🔁 Player CRUD	Add/Edit/Delete players via frontend modal

🧙 Avatars	Avatar URL support per player

📜 Battle storylines	Rich text editor (TinyMCE) for storyline input

📊 Stats dashboard	W/L/D and optional ELO calculation

⚔️ Swiss pairings	Automatic Swiss round match generation

💾 DB schema	All custom tables for tournaments, players, matches

👥 User Linked	Only team owners (logged-in users) can manage their team

🔐 Secure AJAX	Nonce protection and capability checks

📤 Excel export	Full tournament export via PhpSpreadsheet

🌐 Frontend shortcode	[ccg_user_dashboard] — display and manage team

🔗 User dashboard link	yourdomain.com/ccg-tournament-manager/ auto-enabled

🧰 Admin menu	View and manage tournaments from WP admin panel

🖼️ UI polish	Styled modal, responsive dashboard


🔨 TO DO NOW

Structure all plugin files

Zip everything into a WordPress-compatible plugin

🔧 Working on Packaging...

Ensure all JS/CSS are enqueued properly

Package assets, includes, templates, and main plugin file
