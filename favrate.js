/**
 * MediaWiki FavRate extension - javascript part
 * Copyright © 2010+ Vitaliy Filippov
 * License: LGPLv3 or later
 * http://wiki.4intra.net/FavRate
 */

(function()
{
    var favRateTimer = null;
    var favStatus = null;
    var favBtn = null; // last used button

    // AJAX response handler
    var favRateToggleCallback = function(r, btn, pageId, callback, add)
    {
        if (!favStatus)
        {
            favStatus = document.createElement('div');
            favStatus.className = 'favstatus';
            document.body.appendChild(favStatus);
        }
        var p = $(btn).offset();
        var dw = $(document).width();
        if (p.left < dw-200)
        {
            favStatus.style.left = (p.left+8)+'px';
            favStatus.style.right = '';
        }
        else
        {
            favStatus.style.left = '';
            favStatus.style.right = (dw-p.left-8)+'px';
        }
        favStatus.style.top = (p.top+8)+'px';
        favStatus.className = 'favstatus favvisible';
        favStatus.innerHTML = r[1];
        if (r[0])
        {
            btn.className = btn.className.replace(add ? 'fav0' : 'fav1', add ? 'fav1' : 'fav0');
            btn.title = mw.msg(add ? 'favrate-remfav' : 'favrate-addfav');
        }
        favBtn = btn;
        if (callback)
            callback(add);
        clearTimeout(favRateTimer);
        favRateTimer = setTimeout(function() { favStatus.className = 'favstatus'; }, 1500);
    };

    // Start editing the comment, also prevents status window from hiding
    window.favRateStartComment = function(pageId)
    {
        clearTimeout(favRateTimer);
        if (favStatus)
            favStatus.className = 'favstatus favvisible';
        var input = document.getElementById('favrate-comment-'+pageId);
        input.className = 'favcomment';
        input.value = '';
    };

    // Add a comment to favorite
    window.favRateComment = function(pageId)
    {
        var input = document.getElementById('favrate-comment-'+pageId);
        $.ajax({
            type: "POST",
            url: mw.util.wikiScript(),
            data: {
                action: 'ajax',
                rs: 'efFavRateSet',
                rsargs: [ pageId, 1, input.value ]
            },
            dataType: 'json',
            success: function(result)
            {
                favStatus.className = 'favstatus';
                if (input.value != '')
                    favBtn.title = mw.msg('favrate-remfav-cmt', input.value);
            }
        });
    };

    // Toggle favorite for page pageId
    window.favRateToggleFavFor = function(btn, pageId, callback)
    {
        var u = mw.config ? mw.config.get('wgUserName') : wgUserName;
        var add = btn.className.indexOf('fav0') > -1 ? 1 : 0;
        if (pageId && u !== null)
        {
            $.ajax({
                type: "POST",
                url: mw.util.wikiScript(),
                data: {
                    action: 'ajax',
                    rs: 'efFavRateSet',
                    rsargs: [ pageId, add ]
                },
                dataType: 'json',
                success: function(result)
                {
                    favRateToggleCallback(result, btn, pageId, callback, add);
                }
            });
        }
    };

    // Toggle favorite for current page
    window.favRateToggleFav = function(btn)
    {
        var id = mw.config ? mw.config.get('wgArticleId') : wgArticleId;
        favRateToggleFavFor(btn, id);
    };

    // Toggle favorite for a Wikilog comment
    window.favRateToggleFavWikilog = function(link, pageId)
    {
        favRateToggleFavFor(link.parentNode, pageId, function(add) {
            var inc = add ? 1 : -1;
            link.innerHTML = link.innerHTML.replace(/\d+/, function(m) { return parseInt(m[0])+inc; });
        });
    };
})();
