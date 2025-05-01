<?php

namespace Fhoke\Bluetown\Mail;

use Fhoke\Slate\ACF\ACF;
use SlateMail\Mailer as SlateMailMailer;

class Mailer extends SlateMailMailer implements Sender
{
    protected function contentAfter()
    {
        $account_page_id  = ACF::settingsField('endpoint_account');
        $account_page_url = $account_page_id ? \get_permalink($account_page_id) : \home_url();
        $name             = \get_bloginfo('name');
        $current_year     = \date('Y');

        return <<< EOT
                </div>
                <div style="max-width:600px;margin-left:auto;margin-right:auto;padding-top:20px;text-align:center;">
                    <p>
                        <a href="{$account_page_url}">Unsubscribe</a>
                    </p>
                    <p>&copy; $name $current_year</p>
                </div>
            </body>
            </html>
        EOT;
    }
}
