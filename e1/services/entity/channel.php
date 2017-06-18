<?php

namespace e1\services\entity;

use e1\services\coreService;

class channel extends coreService
{
    public function addFeed(\e1\models\channel $channel)
    {
        $feed = $this->app['simplePie']->feed([$channel->url]);

        $items = $feed->get_items();

//        $feed->get_title(); // получаем заголовок RSS-ленты, например "Новости@Mail.Ru"
//        $feed->get_permalink(); // постоянная ссылка новостной ленты, например "http://news.mail.ru/"
//        $feed->get_image_url(); // ссылка на картинку, которую использует фид для самоидентификации в формате .jpg, .gif или ином
//        $feed->get_description(); // забираем краткое описание ленты
//        $feed->get_encoding(); // получаем кодировку документа
//        $feed->get_language(); // узнаём на каком языке выводится данный фид, например "en-us", "ru-ru"

        foreach ($items as $item) {

            $this->app->model('feed')->insert([
                'link' => $item->get_link(),
                'title' => $item->get_title(),
                'content' => $item->get_content(),

                'url' => $channel->url,
                'type' => $channel->type,
                'channel_ids' => [$channel->getKey()],
            ]);

//            $item_date = $item->get_date('Y-m-d H:i:s'); // получаем дату/время в нужном формате
//            $item_title = $item->get_title(); // краткий заголовок новости
//            $item_content = $item->get_content(); // содержание новости
//            $item_link = $item->get_link(); // постоянная ссылка на новость на сайте-источнике
       }

    }
}