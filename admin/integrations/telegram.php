<?php
/**
 * Telegram integration
 */

$telegram_connected = MCBUtils::get_settings_item('telegram_connected', 'false');
$telegram_token = MCBUtils::get_settings_item('telegram_token', '');

?>

<div ng-if="data.messagingClient == 'telegram'">
    <div
        ng-if="<?php echo ($telegram_connected) ? $telegram_connected . '&&' : ''; ?> telegramInvalidated === false && !editTelegramConfig">
        <p>
            <i class="fa fa-check-circle" aria-hidden="true"
               style="color: #29AB29"></i> <?php _e("Telegram Client is configured correctly. <br/>Place the shortcode <code>[contactbot]</code> in a post or page and start chatting with your users :).", 'mcb'); ?>
        </p>

        <p><a href
              ng-click="toggleTelegramConfig(true)"><?php _e('Edit Telegram Configuration', 'mcb'); ?></a>
        </p>
    </div>

    <div ng-if="editTelegramConfig || !<?php echo ($telegram_connected) ? $telegram_connected . '||' : ''; ?> telegramInvalidated">
        <h4><?php _e('How to create your own Telegram bot:', 'mcb'); ?></h4>
        <table style="max-width: 600px;" class="mcb-instructions-table">
            <tr>
                <td colspan="2">
                                        <span
                                            class="inst-number">1</span> <?php _e("First, you'll need a Telegram client. Go and download one at the following link:", 'mcb'); ?>
                    <a href="https://telegram.org/" target="_blank">https://telegram.org/</a>
                    <br/>
                    <br/>
                    <?php _e('Once you have a client installed and ready to go (can be either mobile or desktop), proceed to step 2.', 'mcb'); ?>

                    <div class="down-arrow">
                        <i class="fa fa-arrow-circle-down" aria-hidden="true"></i>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                                        <span
                                            class="inst-number">2</span> <?php _e("Launch your client and strike up a conversation with the <code>@botfather</code>", 'mcb'); ?>

                    <p>
                        <?php _e('Documentation available here:', 'mcb'); ?> <a
                            href="https://core.telegram.org/bots#6-botfather" target="_blank">https://core.telegram.org/bots#6-botfather</a>
                    </p>

                    <div class="notes">
                        <?php _e("The <code>@botfather</code> is a Telegram super-bot which you can use to create new bots. You can find it by typing <code>@botfather</code> in the search bar of your chats tab", 'mcb'); ?>
                    </div>

                    <p>
                        <?php _e("Once you're happily conversing with the <code>@botfather</code>, proceed to step 3", 'mcb'); ?>
                    </p>

                    <div class="down-arrow">
                        <i class="fa fa-arrow-circle-down" aria-hidden="true"></i>
                    </div>
                </td>
                <td>
                    <div class="img-wrapper">
                        <img
                            src="<?php echo MCB_PLUGIN_URL . '/images/telegram-instructions-1.png'; ?>"
                            alt="Telegram instructions 1"/>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                                        <span
                                            class="inst-number">3</span> <?php _e("Create your new contact bot by sending the <code>@botfather</code> the following message:", 'mcb'); ?>
                    <code>/newbot</code>
                    <br/>
                    <br/>

                    <div class="notes">
                        <?php _e("This will create your own Telegram bot. You can chat with this bot as if it was a real Telegram user. All the messages submitted on your site using the Contact Bot, will be delivered to you via this newly created bot.", 'mcb'); ?>
                    </div>

                    <p>
                        <?php _e("Follow the on-screen instructions until you get your API token. Once you do, proceed to step 4.", 'mcb'); ?>
                    </p>

                    <div class="down-arrow">
                        <i class="fa fa-arrow-circle-down" aria-hidden="true"></i>
                    </div>
                </td>
                <td>
                    <div class="img-wrapper">
                        <img
                            src="<?php echo MCB_PLUGIN_URL . '/images/telegram-instructions-2.png'; ?>"
                            alt="Telegram instructions 1"/>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                                        <span
                                            class="inst-number">4</span> <?php _e('OK, now that you have your Bot Token, paste it in the field below:'); ?>
                    <div class="token-store-input">
                        <input type="text" name="telegramToken" id="telegramToken"
                               style="width: 100%"
                               ng-model="telegramToken"
                               ng-init="telegramToken = '<?php echo $telegram_token ? $telegram_token : ''; ?>'"
                               ng-model-options="{ debounce: 750 }"
                               ng-minlength="40"
                               ng-maxlength="50"
                               ng-change="storeTelegramToken(telegramToken)"
                               placeholder="<?php _e('Paste your bot token here...', 'mcb'); ?>"/>

                        <div class="token-store-label"
                             ng-class="{ green: !loading.storeTelegramToken && tokenStored }"
                             ng-if="loading.storeTelegramToken || tokenStored">

                            <div ng-if="loading.storeTelegramToken">
                                <i class="fa fa-circle-o-notch fa-spin"></i> <?php _e("Saving token", 'mcb'); ?>
                            </div>
                            <div ng-if="!loading.storeTelegramToken && tokenStored">
                                <i class="fa fa-check-circle"
                                   aria-hidden="true"></i> <?php _e("Token stored", 'mcb'); ?>
                            </div>
                        </div>

                        <p ng-if="mcbGeneralSettingsForm.telegramToken.$error.minlength"
                           class="mcb-warning">
                            <?php _e("Doesn't look like a valid Telegram token... Make sure you copy the token correctly and try again", 'mcb'); ?>
                        </p>

                        <p ng-if="mcbGeneralSettingsForm.telegramToken.$error.maxlength"
                           class="mcb-warning">
                            <?php _e("Doesn't look like a valid Telegram token... Make sure you copy the token correctly and try again", 'mcb'); ?>
                        </p>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <p>
                        <?php _e("Excellent work!!!. Now, strike up a conversation with your bot (you can either search for it by its username, or simply click the link <code>@botfather</code> gave you when you first created the bot).", 'mcb'); ?>
                    </p>

                    <p>
                        <?php _e("Feel free to send it a few messages to warm things up. Once you're both comfortable, send it the following message:", 'mcb'); ?>
                    </p>

                    <code>I'm the admin, my email
                        is <?php echo wp_get_current_user()->user_email; ?> and my bot token is {{
                        telegramToken || "&lt;enter your telegram token in the field above&gt;"
                        }}</code>
                    <br/>
                    <br/>

                    <div class="notes">
                        <?php _e("Tip: in order to avoid typos, simply copy and paste the message above into the conversation with your bot.", 'mcb'); ?>
                    </div>

                    <div style="margin-top: 10px">
                        <?php _e('Once the message has been successfully sent, click the <b>Connect Telegram</b> button:', 'mcb'); ?>
                    </div>
                    <div class="down-arrow">
                        <i class="fa fa-arrow-circle-down" aria-hidden="true"></i>
                    </div>

                    <div style="text-align: center;">

                        <div class="mcb-error">
                                    <span ng-if="errors.telegramVerificationError == GET_UPDATES_FAILED_ERROR_CODE">
                                        <i class="fa fa-exclamation-circle"
                                           aria-hidden="true"></i> <?php _e("Oh no, your bot did not receive the message :(<br/>This is quite normal actually. Remember, your bot is new to this world. Give it a few more seconds to adjust and then re-send the above message again. Once sent, click the Connect Telegram button and cross your fingers... Good luck!!!", 'mcb'); ?>
                                    </span>

                                    <span
                                        ng-if="errors.telegramVerificationError == ADMIN_VERIFICATION_FAILED_ERROR_CODE">
                                        <i class="fa fa-exclamation-circle"
                                           aria-hidden="true"></i> <?php _e("Admin verification failed.<br/>Please make sure you send the correct token and email and try again.", 'mcb'); ?>
                                    </span>

                                    <span ng-if="errors.telegramVerificationError == UNKNOWN_ERROR_CODE">
                                        <i class="fa fa-exclamation-circle"
                                           aria-hidden="true"></i> <?php _e("Unknown error", 'mcb'); ?>
                                    </span>
                        </div>

                        <a href class="button button-primary" ng-click="verifyTelegram()">
                            <?php _e('Connect Telegram', 'mcb'); ?>
                            <i ng-if="loading.verifyTelegram"
                               class="fa fa-circle-o-notch fa-spin"></i>
                        </a>

                        <div ng-if="telegramVerified">
                            <div class="telegram-verified-confirmation">
                                <i class="fa fa-check-circle" aria-hidden="true"></i>

                                <div
                                    style="font-size: 10px"><?php _e("Telegram Connected Successfully", 'mcb'); ?></div>
                            </div>
                            <div>
                                <?php _e('Proceed to step 5', 'mcb'); ?>
                            </div>
                        </div>

                    </div>
                </td>
                <td>
                    <div class="img-wrapper">
                        <p>
                            <img
                                src="<?php echo MCB_PLUGIN_URL . '/images/telegram-instructions-3.png'; ?>"
                                alt="Telegram instructions 1"/>
                        </p>
                    </div>
                </td>
            </tr>
            <tr ng-if="telegramVerified">
                <td>
                    <div class="down-arrow">
                        <i class="fa fa-arrow-circle-down" aria-hidden="true"></i>
                    </div>
                    <div style="margin: 10px 0">
                        <span class="inst-number">5</span>
                        <b><?php _e("You're done!!! Place the shortcode <code>[contactbot]</code> in a post or page and start chatting with your users :)", 'mcb'); ?></b>

                        <p><?php _e("(That being said, make sure you read the <b>\"How to use\"</b> section before you dive in)", 'mcb'); ?></p>
                    </div>

                </td>
            </tr>
            <tr>
                <td colspan="2">

                </td>
            </tr>
        </table>
    </div>
</div>