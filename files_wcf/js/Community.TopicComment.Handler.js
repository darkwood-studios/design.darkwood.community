"use strict";

/**
 * Initialize Community namespace
 */
// non strict equals by intent
if (window.Community == null) window.Community = { };

/**
 * Namespace for topic comments
 */
Community.TopicComment = { };

/**
 * Topic comment support for Community
 * @author    Julian Pfeil
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 */
Community.TopicComment.Handler = WCF.Comment.Handler.extend({
    /**
     * Initializes the Community.TopicComment.Handler class.
     * 
     * @param	string		containerID
     */
    init: function(containerID) {
        this._commentButtonList = { };
        this._comments = { };
        this._containerID = containerID;
        this._displayedComments = 0;
        this._loadNextComments = null;
        this._loadNextResponses = { };
        this._permalinkComment = null;
        this._permalinkResponse = null;
        this._responseAdd = null;
        this._responseCache = {};
        this._responseRevert = null;
        this._responses = {};
        this._scrollTarget = null;
        this._onResponsesLoaded = null;
        
        this._container = $('#' + $.wcfEscapeID(this._containerID));
        if (!this._container.length) {
            console.debug("[WCF.Comment.Handler] Unable to find container identified by '" + this._containerID + "'");
            return;
        }
        
        this._proxy = new WCF.Action.Proxy({
            success: $.proxy(this._success, this)
        });
        
        this._initComments();
        this._initResponses();
        
        // add new comment
        if (this._container.data('canAdd')) {
            if (elBySel('.commentListAddComment .wysiwygTextarea', this._container[0]) === null) {
                console.error("Missing WYSIWYG implementation, adding comments is not available.");
            }
            else {
                require(['DarkwoodDesign/Community/Ui/Topic/Comment/Add', 'WoltLabSuite/Core/Ui/Comment/Response/Add'], (function (UiTopicCommentAdd, UiCommentResponseAdd) {
                    new UiTopicCommentAdd(elBySel('.jsCommentAdd',  this._container[0]));
                    
                    this._responseAdd = new UiCommentResponseAdd(
                        elBySel('.jsCommentResponseAdd',  this._container[0]),
                        {
                            callbackInsert: (function () {
                                if (this._responseRevert !== null) {
                                    this._responseRevert();
                                    this._responseRevert = null;
                                }
                            }).bind(this)
                        });
                }).bind(this));
            }
        }
        
        require(['WoltLabSuite/Core/Ui/Comment/Edit', 'WoltLabSuite/Core/Ui/Comment/Response/Edit'], (function (UiCommentEdit, UiCommentResponseEdit) {
            new UiCommentEdit(this._container[0]);
            new UiCommentResponseEdit(this._container[0]);
        }).bind(this));
        
        WCF.DOMNodeInsertedHandler.execute();
        WCF.DOMNodeInsertedHandler.addCallback('WCF.Comment.Handler', $.proxy(this._domNodeInserted, this));
        
        WCF.System.ObjectStore.add('WCF.Comment.Handler', this);
        
        window.addEventListener('hashchange', function () {
            var hash = window.location.hash;
            if (hash && hash.match(/.+\/(comment\d+)/)) {
                var commentId = RegExp.$1;
                
                // delay scrolling in case a tab menu change was requested
                window.setTimeout(function () {
                    var comment = elById(commentId);
                    if (comment) comment.scrollIntoView({ behavior: 'smooth' });
                }, 100);
            }
        });
        
        var hash = window.location.hash;
        if (hash.match(/^#(?:[^\/]+\/)?comment(\d+)(?:\/response(\d+))?/)) {
            var comment = elById('comment' + RegExp.$1);
            if (comment) {
                var response;
                if (RegExp.$2) {
                    response = elById('comment' + RegExp.$1 + 'response' + RegExp.$2);
                    if (response) {
                        this._scrollTo(response, true);
                    }
                    else {
                        // load response on-the-fly
                        this._loadResponseSegment(comment, RegExp.$1, RegExp.$2);
                    }
                }
                else {
                    this._scrollTo(comment, true);
                }
            }
            else {
                // load comment on-the-fly
                this._loadCommentSegment(RegExp.$1, RegExp.$2);
            }
        }
    }
});