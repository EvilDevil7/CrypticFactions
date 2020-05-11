<?php

namespace core\discord;

use core\discord\task\DiscordPost;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class DiscordManager{

    /**
     * @param string $url
     * @param string $content
     * @param string $username
     * @param array $embed
     */
    public static function postWebhook(string $url, string $content, string $username, array $embed = []): void{
        $data = [
            "username" => $username,
            "content" => $content
        ];
        if(!empty($embed)){
            $data["embeds"] = $embed;
            unset($data["content"]);
        }else{
            $msg = $data["content"];
            $msg = str_replace("@everyone", "(@)everyone", $msg);
            $msg = str_replace("@here", "(@)here", $msg);
            $data["content"] = $msg;
        }
        $con = json_encode($data);
        $post = new DiscordPost("https://discordapp.com/api/webhooks/" . $url, $con);
        Server::getInstance()->getAsyncPool()->submitTask($post);
    }

    /**
     * @param string $sender
     * @param string $player
     * @param string $type
     * @param array $details
     * @param string $reason
     */
    public static function sendPunishment(string $sender, string $player, string $type, array $details = [], string $reason = "None"): void{
        $webhook = "690668001787117720/U0YsHX_9st5TR5XE6xITMlyDquty1Udx-FU7uiw0vkrWjiOUKFk7Z5EVTU4N_OrdSRjN";
        $punishment = "**Player:** " . $player;
        $punishment .= "\n**By**: " . $sender;
        $punishment .= "\n**Reason**: " . $reason;
        foreach($details as $string){
            $punishment .= TextFormat::EOL . $string;
        }
        DiscordManager::postWebhook($webhook, "", $player . " Punishment", [
            [
                "color" => 0xFFFF00,
                "title" => "Punishment: " . $type,
                "description" => $punishment
            ]
        ]);
    }
}
