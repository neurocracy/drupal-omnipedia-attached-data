// -----------------------------------------------------------------------------
//   Omnipedia - Attached data - Wikimedia links
// -----------------------------------------------------------------------------

// This finds Wikimedia links that have attached data and enhances them with the
// content pop-up component, displaying the text as either a tooltip or an
// off-canvas panel, depending on the screen width.

AmbientImpact.on(['contentPopUp'], function(aiContentPopUp) {
AmbientImpact.addComponent('OmnipediaWikimediaLink', function(
  OmnipediaWikimediaLink, $
) {
  'use strict';

  /**
   * Link selector to find Wikimedia links that have attached data.
   *
   * Note how this only matches <a> elements that have all three data
   * attributes.
   *
   * @type {String}
   */
  var linkSelector = 'a[' +
    drupalSettings.omnipedia.attachedData.isWikimediaLinkAttributeName +
  '][' +
    drupalSettings.omnipedia.attachedData.titleAttributeName +
  '][' +
    drupalSettings.omnipedia.attachedData.contentAttributeName +
  ']';

  /**
   * Textarea element used to decode HTML entities in attached data attributes.
   *
   * @type {HTMLElement}
   *
   * @see decodeEntities()
   */
  var decodeTextArea = document.createElement('textarea');

  /**
   * Decode HTML entities in a safe and secure manner.
   *
   * @param {String} encodedString
   *   The string of HTML to decode entities of.
   *
   * @return {String}
   *   The encodedString parameter with HTML entities decoded safely.
   *
   * @see https://stackoverflow.com/a/1395954
   *   Uses this technique to avoid XSS issues.
   *
   * @see decodeTextArea
   */
  function decodeEntities(encodedString) {
    decodeTextArea.innerHTML = encodedString;

    return decodeTextArea.value;
  }

  this.addBehaviour(
    'OmnipediaWikimediaLink',
    'omnipedia-wikimedia-link',
    '.layout-container',
    function(context, settings) {
      /**
       * All Wikimedia links in context that have attached data attributes.
       *
       * @type {jQuery}
       */
      var $links = $(this).find(linkSelector);

      // Don't do anything if we can't find any links.
      if ($links.length === 0) {
        return;
      }

      // Add a tabindex attribute to any links found that don't have an 'href'
      // attribute so that they're still keyboard accessible. This happens if
      // the filter to strip Wikimedia links is enabled.
      $links.filter(':not([href])').attr('tabindex', 0);

      $links.one('contentPopUpContent.OmnipediaWikimediaLink', function(
        event, $title, $content
      ) {
        /**
         * The current Wikimedia link to build attached data content for.
         *
         * @type {jQuery}
         */
        var $this = $(this);

        /**
         * The HTML string content of this Wikimedia link's attached data.
         *
         * @type {String}
         */
        var content = '';

        $title.append($this.attr(
          drupalSettings.omnipedia.attachedData.titleAttributeName
        ));

        // Attempt to parse the attached data content as HTML using the native
        // DOMParser(). This should allow us to safely parse markup while
        // guarding against XSS.
        //
        // @see https://stackoverflow.com/a/34064434
        //
        // @see https://developer.mozilla.org/en-US/docs/Web/API/DOMParser
        //
        // @see https://developer.mozilla.org/en-US/docs/Web/API/Element/innerHTML#Security_considerations
        try {
          /**
           * A document parsed from the attached data content attribute.
           *
           * @type {Document}
           */
          var parsedDocument = new DOMParser().parseFromString(
            decodeEntities(
              $this.attr(
                drupalSettings.omnipedia.attachedData.contentAttributeName
              )
            ),
            'text/html'
          );

          /**
           * The HTML content of the parsed attached data content attribute.
           *
           * @type {String}
           */
          var parsedContent = $(parsedDocument.body).html();

          // If we got a string that's not empty, use it as the content.
          if (
            typeof parsedContent === 'string' &&
            parsedContent.length > 0
          ) {
            content = parsedContent;
          }

        } catch (error) {
          console.error(error);
        }

        // If no content was parsed as HTML or there was an error, fall back to
        // using the title attribute, which should contain a plain text version
        // of the attached data.
        if (content.length === 0) {
          content = $this.attr('title');
        }

        $content.append(content);
      });

      aiContentPopUp.addItems($links, {tooltip: {
        insertCallback: function($tooltip, $trigger) {

          /**
           * The ancestor element that tooltips should be placed after.
           *
           * This avoids issues with inheriting formatting and font size from
           * elements that the tooltip may be placed inside of, by placing the
           * tooltip just after all of these elements.
           *
           * Since jQuery().parents() can filter by a provided selector,
           * starting from closest and going up the tree. By using
           * jQuery().last(), we can reduce the set down to the highest level
           * ancestor, i.e. the one that potentially contains one or more of
           * these, i.e. multiple matching parents. For example, if an infobox
           * contains a media element, this will choose the infobox as that's
           * higher up the tree.
           *
           * @type {jQuery}
           *
           * @see https://api.jquery.com/parents/
           */
          var $container = $trigger.parents([
            '.omnipedia-infobox',
            '.omnipedia-media-group',
            '.omnipedia-media',
            'strong',
            'em',
            'sup',
          ].join(',')).last();

          // If one of the above containers contains the trigger, insert the
          // tooltip after the container.
          if ($container.length > 0) {
            $tooltip.insertAfter($container);

            return;
          }

          // If none of the above containers are found, just insert the tooltip
          // after the triggering element.
          $tooltip.insertAfter($trigger);
        }
      }});
    },
    function(context, settings, trigger) {
      /**
       * All Wikimedia links in context that have attached data attributes.
       *
       * @type {jQuery}
       */
      var $links = $(this).find(linkSelector);

      // Don't do anything if we can't find any links.
      if ($links.length === 0) {
        return;
      }

      aiContentPopUp.removeItems($links);

      // Remove the handler in case it's still attached, e.g. there was an error
      // somewhere and it didn't get triggered at all.
      $links.off('contentPopUpContent.OmnipediaWikimediaLink');
    }
  );
});
});
