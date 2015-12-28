<?php

namespace Discord\WebSockets\Events;

use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class MessageUpdate extends Event
{
	/**
	 * Returns the formatted data.
	 *
	 * @param array $data 
	 * @param Discord $discord 
	 * @return Message 
	 */
	public function getData($data, $discord)
	{
		return new Message([
			'id'                => $data->id,
            'channel_id'        => $data->channel_id,
            'content'           => $data->content,
            'mentions'          => $data->mentions,
            'author'            => $data->author,
            'mention_everyone'  => $data->mention_everyone,
            'timestamp'         => $data->timestamp,
            'edited_timestamp'  => $data->edited_timestamp,
            'tts'               => $data->tts,
            'attachments'       => $data->attachments,
            'embeds'            => $data->embeds
		], true);
	}

	/**
	 * Updates the Discord instance with the new data.
	 *
	 * @param mixed $data 
	 * @param Discord $discord 
	 * @return Discord 
	 */
	public function updateDiscordInstance($data, $discord)
	{
		foreach ($discord->guilds as $index => $guild) {
			foreach ($guild->channels as $cindex => $channel) {
				if ($channel->id == $data->channel_id) {
					foreach ($channel->messages as $mindex => $message) {
						if ($message->id == $data->id) {
							$channel->messages->pull($mindex);
							$channel->messages->push($data);
						
							break;
						}
					}

					$guild->channels->pull($cindex);
					$guild->channels->push($channel);

					break;
				}
			}

			$discord->guilds->pull($index);
			$discord->guilds->push($guild);
		}

		return $discord;
	}
}