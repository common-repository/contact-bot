<div ng-app="mcbApp">
    <div ng-controller="ChatCtrl">
        <div id="messageBox" schroll-bottom="messages">
            <div id="messageList">
                <div ng-repeat="message in messages" class="chatMessage mcb-fade"
                    ng-class="{ client: message.src == 'client', admin: message.src == 'admin' } ">
                    <span class="content">
                        {{ message.text }}
                        <br/>
                    </span>
                </div>
            </div>
        </div>

        <form id="chatBox" name="chatBox" ng-submit="submitMessage(chatMessage, 'client')">
            <input id="chatMessageInput" type="text"
                   ng-model="chatMessage"
                   placeholder="<?php _e( 'Type a message...', 'mcb' ); ?>" autocomplete="off" />
            <br/>
            <br/>
            <button type="submit"><?php _e('Send', 'mcb'); ?></i></button>
        </form>

        <div ng-if="DEBUG" style="padding: 20px">
            <button ng-click="getUpdates()">GET UPDATES (temp)</button>
        </div>
    </div>
</div>