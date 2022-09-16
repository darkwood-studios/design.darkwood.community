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

import DomChangeListener from "WoltLabSuite/Core/Dom/Change/Listener";
import DomUtil from "WoltLabSuite/Core/Dom/Util";
import * as Language from "WoltLabSuite/Core/Language";
import * as UiNotification from "WoltLabSuite/Core/Ui/Notification";
import UiCommentAdd from "WoltLabSuite/Core/Ui/Comment/Add";

interface AjaxResponse {
  returnValues: {
    guestDialog?: string;
    template: string;
  };
}

class UiTopicCommentAdd extends UiCommentAdd {
  /**
   * Inserts the rendered message.
   */
  protected _insertMessage(data: AjaxResponse): HTMLElement {
    // insert HTML
    DomUtil.insertHtml(data.returnValues.template, this._container, "before");

    UiNotification.show(Language.get("wcf.global.success.add"));

    DomChangeListener.trigger();

    return this._container.nextElementSibling as HTMLElement;
  }
}

export = UiTopicCommentAdd;
