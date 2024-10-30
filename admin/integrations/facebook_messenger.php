<?php
/**
 * Facebook messenger integration
 */

$fbm_connected = MCBUtils::get_settings_item('fbm_connected', 'false');
$fbm_token = MCBUtils::get_settings_item('fbm_token', '');

?>

<div ng-if="data.messagingClient == 'fb-messenger'" class="fbm-container">
    <input type="hidden" value="<?php echo ($fbm_token ? $fbm_token : '');?>" ng-model="fbmToken" id="fbm-token-input" />

    <!-- Connected and running -->
    <div
        ng-if="<?php echo ($fbm_connected ? $fbm_connected : 'false'); ?> && fbmInvalidated === false && !editFbmConfig">
        <p>
            <i class="fa fa-check-circle" aria-hidden="true"
               style="color: #29AB29"></i> <?php _e("Facebook Messenger Client is configured correctly. <br/>Place the shortcode <code>[contactbot]</code> in a post or page and start chatting with your users :).", 'mcb'); ?>
        </p>

        <p><a href
              ng-click="toggleFbmConfig(true)"><?php _e('Edit Facebook Messenger Configuration', 'mcb'); ?></a>
        </p>
    </div>

    <!-- Configuration -->
    <div ng-if="editFbmConfig || !<?php echo ($fbm_connected ? $fbm_connected : 'false'); ?> || fbmInvalidated">

        <h4><?php _e( 'How to connect Facebook Messenger:', 'mcb' ); ?></h4>
        <table style="max-width: 600px;" class="mcb-instructions-table">
            <tr>
                <td width="30%">
                    <span class="inst-number">1</span>
                    <?php _e( "First, we're going to need a token:" ); ?>
                </td>
                <td width="70%">
                    <span ng-if="fbmToken" class="fbm-token">{{ fbmToken }}</span>
                    <span ng-if="!fbmToken">
                        <?php _e( "No token yet. Please click the <b>generate</b> button to create one.", "mcb" ); ?>
                    </span>
                    <div style="margin-top: 5px">
                        <a href class="button button-secondary" ng-click="generateFbmToken()">Generate
                            <i ng-if="loading.generateFbmToken" class="fa fa-circle-o-notch fa-spin"></i></a>
                    </div>
                </td>
            </tr>
            <tr ng-if="fbmToken">
                <td width="30%">
                    <span class="inst-number">2</span>
                    <?php _e( 'Now, strike up a conversation with <a target="_blank" href="http://m.me/552876388226850">Contact Bot</a> and send it the following message:', 'mcb'); ?>

                    <div class="down-arrow">
                        <i class="fa fa-arrow-circle-down" aria-hidden="true"></i>
                    </div>
                </td>
                <td width="70%">
                    <code>
                        I'm the admin, my email is <?php echo wp_get_current_user()->user_email; ?>  and my bot token is {{ fbmToken }}
                    </code>

                    <div class="notes" style="margin-top: 10px;">
                        <?php _e("Tip: in order to avoid typos, simply copy and paste the message above into the conversation with Contact Bot.", 'mcb'); ?>
                    </div>

                    <div style="margin-top: 10px;">
                        <a class="button button-secondary" target="_blank" href="http://m.me/552876388226850">Go talk to Contact Bot</a>
                    </div>

                </td>
            </tr>
            <tr ng-if="fbmToken">
                <td>
                    <a href class="button button-primary" ng-click="verifyFbm()">
                        <?php _e('Connect Messenger', 'mcb'); ?>
                        <i ng-if="loading.verifyFbm"
                           class="fa fa-circle-o-notch fa-spin"></i>
                    </a>

                    <div ng-if="fbmVerified">
                        <div class="fbm-verified-confirmation">
                            <i class="fa fa-check-circle" aria-hidden="true"></i>

                            <div
                                style="font-size: 10px"><?php _e("Facebook Messenger Connected Successfully", 'mcb'); ?></div>

                        </div>

                    </div>
                </td>
                <td ng-if="fbmVerified">
                    <div>
                        <span class="inst-number">3</span>
                        <b><?php _e("You're done!!! Place the shortcode <code>[contactbot]</code> in a post or page and start chatting with your users :)", 'mcb'); ?></b>

                        <p><?php _e("(That being said, make sure you read the <b>\"How to use\"</b> section before you dive in)", 'mcb'); ?></p>
                    </div>

                </td>
            </tr>
            <tr ng-if="fbmToken">
                <td colspan="2">
                    <div class="mcb-error">
                        <span ng-if="errors.fbmVerificationError == FBM_ADMIN_VERIFICATION_FAILED">
                            <i class="fa fa-exclamation-circle"
                               aria-hidden="true"></i> <?php _e("Oh no, we were unable to link your bot :( Please try to generate a new token and resend the snippet above to the Facebook Messenger Contact Bot. Once that's done, try connecting it again. Good luck!", 'mcb'); ?>
                        </span>
                    </div>
                </td>
            </tr>


        </table>
    </div>

</div>