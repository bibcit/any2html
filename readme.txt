=== Bibcit Any2HTML ===
Contributors:      bibcithelp,rastermechanism,leibra
Tags:              markdown, pdf, image, editor, converter, html converter
Requires at least: 6.5
Tested up to:      6.9
Requires PHP:      8.0
Stable tag:        1.1.0
License:           GPL-2.0-or-later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

Convert Markdown or file (pdf/image) to HTML directly inside the WordPress post editor using the Bibcit API.

== Description ==

https://youtu.be/n4HOEXshfNg

Bibcit Any2HTML adds a Markdown input panel to the post editor. Paste your Markdown, click Convert to HTML, and the resulting HTML is inserted straight into the editor — no copy-pasting required.

Features:

1. API key management with one-click validation.
2. Works with the Classic Editor, Gutenberg (block editor), and TinyMCE
3. Markdown or file to HTML conversion powered by the Bibcit API
4. Converted html automatically added to your content of post

== Requirements ==

A Bibcit account and API key — sign up at bibcit.com

= Third-Party Service =

This plugin connects to the Bibcit API (api.bibcit.com), an external service operated by Bibcit.

Data transmitted: Your Markdown text or uploaded file (PDF/image) is sent to Bibcit's API solely for conversion. Your API key is included as a request header (Bibcit-Key) for authentication.
When it's sent: Only on clicking "Convert to HTML", and only when the plugin is enabled with a valid API key.
Privacy: Data is processed in real-time and not stored, logged, or retained by this plugin (Bibcit Any2HTML) beyond fulfilling the request — ensuring full privacy of your content.

APIs used by this plugin:
* MassiveMark (Markdown/text to HTML): https://github.com/bibcit/MassiveMark
* MassivePix (PDF/image to HTML): https://github.com/bibcit/MassivePix

Please review Bibcit's policies before use:

**[Privacy Policy](https://www.bibcit.com/en/privacy)**
**[Terms of Service](https://www.bibcit.com/en/terms)**

== Installation ==

## Installation

### Installation from within WordPress
1. Visit **Plugins > Add New**.
2. Search for **Bibcit Any2HTML**.
3. Install and activate the **Bibcit Any2HTML** plugin.

---

### Manual installation
1. Upload the entire `Bibcit Any2HTML` folder to the `/wp-content/plugins/` directory.
2. Visit **Plugins**.
3. Activate the **Bibcit Any2HTML** plugin.

### Enabling the plugin
1. Go to Settings → Bibcit Any2HTML.
2. Enter your Bibcit API key and click Validate Key.
3. Save Settings.
4. Open any post or page — the Bibcit Any2HTML meta box will appear below the editor.
5. You can choose to enter your markdown or upload a file (pdf or image) and convert to html

== Frequently Asked Questions ==

= Where do I get an API key? =

Sign up at bibcit.com to obtain your API key.

= Is my content stored by Bibcit? =

No. Your content is processed in real-time and not stored or retained. See our Privacy Policy for full details.


= The meta box does not appear in the editor. =

Make sure both conditions are met in Settings → Any2HTML: the API key status shows ✔ Valid and the Enable Markdown Conversion toggle is on.

= What happens if my API key expires or becomes invalid? =

The plugin will display an error message in the editor with a direct link to the settings page so you can re-validate or update your key.

== Screenshots ==

1. Settings page — API key validation 
2. Post editor — Markdown input panel with Convert button.

== Source Code ==

The full source code, including all JavaScript source files and build configuration, is publicly available on [GitHub](https://github.com/bibcit/any2html)


== Changelog ==

= 1.1.0 =
* Added file upload support (PDF/image) for HTML conversion.
* Minor fixes and performance improvements.

= 1.0.0 =

Initial release.
== Upgrade Notice ==

= 1.1.0 = Adds file upload (PDF/image) for HTML conversion, plus minor fixes and performance improvements.

= 1.0.0 = Initial release.