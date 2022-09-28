/**
 * Handles adding comments in topic
 *
 * @author    Julian Pfeil
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 * @module  DarkwoodDesign/Community/Ui/Comment/Add
 * @woltlabExcludeBundle tiny
 */
define(["require", "exports", "tslib", "WoltLabSuite/Core/Dom/Change/Listener", "WoltLabSuite/Core/Dom/Util", "WoltLabSuite/Core/Language", "WoltLabSuite/Core/Ui/Notification", "WoltLabSuite/Core/Ui/Comment/Add"], function (require, exports, tslib_1, Listener_1, Util_1, Language, UiNotification, Add_1) {
    "use strict";
    Listener_1 = (0, tslib_1.__importDefault)(Listener_1);
    Util_1 = (0, tslib_1.__importDefault)(Util_1);
    Language = (0, tslib_1.__importStar)(Language);
    UiNotification = (0, tslib_1.__importStar)(UiNotification);
    Add_1 = (0, tslib_1.__importDefault)(Add_1);
    class UiTopicCommentAdd extends Add_1.default {
        /**
         * Inserts the rendered message.
         */
        _insertMessage(data) {
            // insert HTML
            Util_1.default.insertHtml(data.returnValues.template, this._container, "before");
            UiNotification.show(Language.get("wcf.global.success.add"));
            Listener_1.default.trigger();
            return this._container.nextElementSibling;
        }
    }
    return UiTopicCommentAdd;
});
