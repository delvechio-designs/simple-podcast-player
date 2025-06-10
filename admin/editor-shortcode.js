/*
 * Adds a “Podcast Player” button to:
 *  – Classic Editor toolbar
 *  – Classic Paragraph block toolbar
 *  – QuickTags (Text tab)
 */

(function () {
  // Gutenberg classic block / TinyMCE
  if (typeof tinymce !== "undefined") {
    tinymce.PluginManager.add("spp_mce_button", function (editor) {
      editor.ui.registry.addButton("spp_mce_button", {
        text: "Podcast Player",
        icon: "audio",
        tooltip: "Insert podcast player shortcode",
        onAction: function () {
          editor.insertContent(
            '[podcast_player audio="" title="" subtitle=""]'
          );
        },
      });
    });
  }

  // QuickTags (Text tab)
  if (typeof QTags !== "undefined") {
    QTags.addButton(
      "spp_qt",
      "Podcast Player",
      '[podcast_player audio="" title="" subtitle=""]'
    );
  }
})();
