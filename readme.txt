=== Bibcit Any2HTML ===
Contributors:      bibcithelp,leibra
Tags:              markdown, editor, converter, bibcit, html
Requires at least: 5.9
Tested up to:      6.9
Requires PHP:      7.4
Stable tag:        1.0.0
License:           GPL-2.0-or-later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

Convert Markdown to HTML directly inside the WordPress post editor using the Bibcit API.

== Description ==

Bibcit Any2HTML adds a **Markdown input panel** to the post editor. Paste your Markdown, click **Convert to HTML**, and the resulting HTML is inserted straight into the editor — no copy-pasting required.

**Features**

* Markdown-to-HTML conversion powered by the Bibcit API
* Works with the Classic Editor, Gutenberg (block editor), and TinyMCE
* API key management with one-click validation
* Enable / disable toggle — locked until a valid API key is confirmed
* Automatic key invalidation when the API returns an unauthorized response, with a direct re-validate link

**Requirements**

* A Bibcit account and API key — sign up at [bibcit.com](https://www.bibcit.com/en)

= Third-Party Service =

This plugin relies on the **Bibcit API** ([api.bibcit.com](https://api.bibcit.com)), an external service operated by Bibcit.

* **What data is sent:** The Markdown text you enter in the conversion panel is transmitted to `https://api.bibcit.com/api/massivemark/mtoh` for processing. Your API key is sent as a request header (`Bibcit-Key`) for authentication.
* **When data is sent:** Only when you click the "Convert to HTML" button, and only if the plugin is enabled and the API key is valid.
* **No data is stored externally** beyond what is necessary to fulfil the conversion request.
* **API Documentation:** [github.com/bibcit/MassiveMark](https://github.com/bibcit/MassiveMark/)

Please review Bibcit's policies before use:

* [Privacy Policy](https://www.bibcit.com/en/privacy)
* [Terms of Service](https://www.bibcit.com/en/terms)

== Installation ==

1. Upload the `any2html` folder to `/wp-content/plugins/`.
2. Activate the plugin through **Plugins → Installed Plugins**.
3. Go to **Settings → Bibcit Any2HTML**.
4. Enter your Bibcit API key and click **Validate Key**.
5. Enable the **Enable Markdown Conversion** toggle and save.
6. Open any post or page — the **Bibcit Any2HTML — Markdown Converter** meta box will appear below the editor.

== Frequently Asked Questions ==

= Where do I get an API key? =

Sign up at [bibcit.com](https://www.bibcit.com/en) to obtain your API key.

= Is my content stored by Bibcit? =

Please refer to the [Bibcit Privacy Policy](https://www.bibcit.com/en/privacy) for details on data handling.

= The meta box does not appear in the editor. =

Make sure both conditions are met in **Settings → Any2HTML**: the API key status shows **✔ Valid** and the **Enable Markdown Conversion** toggle is on.

= What happens if my API key expires or becomes invalid? =

The plugin will display an error message in the editor with a direct link to the settings page so you can re-validate or update your key.

== Screenshots ==

1. Settings page — API key validation and enable toggle.
2. Post editor — Markdown input panel with Convert button.

== Changelog ==

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.0.0 =
Initial release.
