<?php

namespace bot\WebBundle\Controller;

class JsonParse
{
    public function chooseLanguage()
    {
        $t = array(
            'text' => 'Та хэл сонгоно уу! Please choose your language!',
            'quick_replies' => array(
                array(
                    'content_type' => 'text',
                    'title' => 'Монгол хэл',
                    'image_url' => 'https://www.turkishexporter.net/i/bayrak/24/MG-flag.png',
                    'payload' => 'USER_MONGOLIA'
                ),
                array(
                    'content_type' => 'text',
                    'title' => 'English',
                    'image_url' => 'http://r-ec.bstatic.com/static/img/flags/24/us/e39c170c852301a1817b3d0833be23f677a2f922.png',
                    'payload' => 'USER_ENGLISH'
                )
            )
        );
        return $t;
    }

    public function mainText($a, $lang, $type, $username = null)
    {
        $arr = array();
        if ($type == 1) // medee
        {
            $medee = array();

            foreach ($a as $ak) {
                if ($lang == 0) {
                    array_push($medee,
                        array(
                            'title' => $ak['titleEn'],
                            "image_url" => $ak['img'],
                            "subtitle" => $ak['descEn'],
                            "default_action" =>
                                array(
                                    "type" => "web_url",
                                    "url" => 'https://21018f1c.ngrok.io/bot/news/' . $ak['id'] . '?rdt=' . $ak['link'],
                                ),
                            "buttons" =>
                                array(
                                    array(
                                        "type" => "web_url",
                                        "url" => 'https://21018f1c.ngrok.io/bot/news/' . $ak['id'] . '?rdt=' . $ak['link'],
                                        "title" => "more"
                                    )
                                )
                        )
                    );
                } else {
                    array_push($medee,
                        array(
                            'title' => $ak['title'],
                            "image_url" => $ak['img'],
                            "subtitle" => $ak['desc'],
                            "default_action" =>
                                array(
                                    "type" => "web_url",
                                    "url" => 'https://21018f1c.ngrok.io/bot/news/' . $ak['id'] . '?rdt=' . $ak['link'],
                                ),
                            "buttons" =>
                                array(
                                    array(
                                        "type" => "web_url",
                                        "url" => 'https://21018f1c.ngrok.io/bot/news/' . $ak['id'] . '?rdt=' . $ak['link'],
                                        "title" => "дэлгэрэнгүй"
                                    )
                                )
                        )
                    );
                }
            }
            $arr =
                array(
                    'attachment' => array(
                        'type' => 'template',
                        'payload' =>
                            array(
                                'template_type' => 'generic',
                                'elements' => $medee
                            )
                    )
                );

        } elseif ($type == 2) //text
        {
            if ($a['buttons'] != null) {
                $text = null;
                $btn = array();
                if ($lang == 1) {
                    $text = $a['title'];
                    foreach ($a['buttons'] as $b) {
                        array_push($btn, array(
                            'type' => 'postback',
                            'title' => $b['name'],
                            'payload' => 'USER_NEWS_' . $b['blockId']
                        ));
                    }
                } else {
                    $text = $a['titleEn'];
                    foreach ($a['buttons'] as $b) {
                        array_push($btn, array(
                            'type' => 'postback',
                            'title' => $b['nameEn'],
                            'payload' => 'USER_NEWS_' . $b['blockId']
                        ));
                    }
                }

                if ($username != null) {
                    $needle = "{{user_name}}";

                    if (strpos($text, $needle) !== false) {
                        $text = str_replace($needle, $username, $text);
                    }
                }
                $arr = array(
                    'attachment' => array(
                        'type' => 'template',
                        'payload' => array(
                            'template_type' => 'button',
                            'text' => $text,
                            'buttons' => $btn
                        )
                    )
                );
            } else {
//                var_dump($a['title']);
                if ($username != null) {
                    $needle = "{{user_name}}";
                    if (strpos($a['title'], $needle) !== false) {
                        $a['title'] = str_replace($needle, $username, $a['title']);
                    }
                }

                if ($lang == 1) {
                    $arr = array(
                        "text" => $a['title']
                    );
                } else {
                    $arr = array(
                        "text" => $a['titleEn']
                    );
                }
            }

        } elseif ($type == 4) //text Text
        {
            if ($a['buttons'] != null) {
                $text = null;
                $btn = array();
                if ($lang == 1) {
                    $text = $a['desc'];
                    foreach ($a['buttons'] as $b) {
                        array_push($btn, array(
                            'type' => 'postback',
                            'title' => $b['name'],
                            'payload' => 'USER_NEWS_' . $b['blockId']
                        ));
                    }
                } else {
                    $text = $a['descEn'];
                    foreach ($a['buttons'] as $b) {
                        array_push($btn, array(
                            'type' => 'postback',
                            'title' => $b['nameEn'],
                            'payload' => 'USER_NEWS_' . $b['blockId']
                        ));
                    }
                }


                if ($username == null) {
                    $needle = "{{user_name}}";
                    if (strpos($text, $needle) !== false) {
                        $text = str_replace($needle, $username, $text);
                    }
                }

                $arr = array(
                    'attachment' => array(
                        'type' => 'template',
                        'payload' => array(
                            'template_type' => 'button',
                            'text' => $text,
                            'buttons' => $btn
                        )
                    )
                );
            } else {
                if ($username == null) {
                    $needle = "{{user_name}}";
                    if (strpos($a['desc'], $needle) !== false) {
                        $a['desc'] = str_replace($needle, $username, $a['desc']);
                    }
                }
                if ($lang == 1) {
                    $arr = array(
                        "text" => $a['desc']
                    );
                } else {
                    $arr = array(
                        "text" => $a['descEn']
                    );
                }
            }

        } elseif ($type == 3) //zurag
        {
            if ($a['buttons'] != null) {
                $btn = array();
                if ($lang == 1) {
                    foreach ($a['buttons'] as $b) {
                        array_push($btn,
                            array(
                                'title' => 'mobicom',
                                "image_url" => $a['img'],
                                "buttons" => array(array(
                                    'type' => 'postback',
                                    'title' => $b['name'],
                                    'payload' => 'USER_NEWS_' . $b['blockId']
                                ))
                            )

                        );
                    }
                } else {
                    foreach ($a['buttons'] as $b) {
                        array_push($btn,
                            array(
                                'title' => '*',
                                "image_url" => $a['img'],
                                "buttons" => array(array(
                                    'type' => 'postback',
                                    'title' => $b['nameEn'],
                                    'payload' => 'USER_NEWS_' . $b['blockId']
                                ))
                            )

                        );
                    }
                }

                $arr = array(
                    'attachment' => array(
                        'type' => 'template',
                        'payload' =>
                            array(
                                'template_type' => 'generic',
                                'elements' => $btn
                            )
                    )
                );
            } else {
                $arr = array(
                    "attachment" => array(
                        "type" => "image",
                        "payload" => array(
                            "url" => $a['img']
                        )
                    )
                );
            }
        } else {

        }
        return $arr;
    }
}



//
//
//                // ========= Button with text templete ==========
//                $jsonBtnText =
//                    array(
//                        'attachment' =>    array(
//                            'type' => 'template',
//                            'payload' =>  array(
//                                'template_type' => 'button',
//                                'text' => 'Mobicom Messenger-т тавтай морилно уу!',
//                                'buttons' =>
//                                    array(
//                                        array(
//                                            'type' => 'postback',
//                                            'title' => 'Шинэ үйлчилгээ',
//                                            'payload' => 'USER_NEW_SERVICE'
//                                        ),
//                                        array(
//                                            'type' => 'postback',
//                                            'title' => 'Шинэ мэдээ',
//                                            'payload' => 'USER_NEW_NEWS'
//                                        )
//                                    )
//                            )
//                        )
//                    ) ;
//
//
//                // ========= List templete ==========
//                $jsonNewsList =
//                    array(
//                        'attachment' =>    array(
//                            'type' => 'template',
//                            'payload' =>
//                                array(
//                                'template_type' => 'generic',
//                                'elements' =>
//                                    array(
//                                        array(
//                                            'title' => "Classic T-Shirt Collection",
//                                            "image_url"=> "http://gstat.mn/newsn/images/c/2017/01/199292-05012017-1483595741-890449369-munkhbat.jpg",
//                                            "subtitle"=> "See all our colors",
//                                            "default_action"=>
//                                                array(
//                                                    "type" =>  "web_url",
//                                                    "url" => "https://www.yolo.mn",
//                                                )
//                                                ,
//                                            "buttons" =>
//                                                array(
//                                                    array(
//                                                        "type" => "web_url",
//                                                        "url" => "https://www.yolo.mn",
//                                                        "title" =>  "Visit"
//                                                    )
//                                                )
//                                        ),
//                                        array(
//                                            'title' => "Classic T-Shirt Collection",
//                                            "image_url"=> "http://gstat.mn/newsn/images/c/2017/01/199292-05012017-1483595741-890449369-munkhbat.jpg",
//                                            "subtitle"=> "See all our colors",
//                                            "default_action"=>
//                                                array(
//                                                    "type" =>  "web_url",
//                                                    "url" => "https://www.yolo.mn",
//                                                )
//                                        ,
//                                            "buttons" =>
//                                                array(
//                                                    array(
//                                                        "type" => "web_url",
//                                                        "url" => "https://www.yolo.mn",
//                                                        "title" =>  "Visit"
//                                                    )
//                                                )
//                                        ),
//                                        array(
//                                        'title' => "Classic T-Shirt Collection",
//                                        "image_url"=> "http://gstat.mn/newsn/images/c/2017/01/199292-05012017-1483595741-890449369-munkhbat.jpg",
//                                        "subtitle"=> "See all our colors",
//                                        "default_action"=>
//                                            array(
//                                                "type" =>  "web_url",
//                                                "url" => "https://www.yolo.mn",
//                                            )
//                                    ,
//                                        "buttons" =>
//                                            array(
//                                                array(
//                                                    "type" => "web_url",
//                                                    "url" => "https://www.yolo.mn",
//                                                    "title" =>  "Visit"
//                                                )
//                                            )
//                                    )
//                                    )
//                            )
//                        )
//                    );
//
//                // ========= text templete ==========
//                $jsonText = array(
//                    "text" =>"hello, world!"
//                );
//
//                // ========= Image templete ==========
//                $jsonImg = array(
//                    "attachment" => array(
//                        "type" => "image",
//                        "payload" => array(
//                            "url" => "http://gstat.mn/newsn/images/c/2017/01/199292-05012017-1483595741-890449369-munkhbat.jpg"
//                        )
//                    )
//                );
//
//                $json = $jsonNewsList;
//                $jsonData = json_encode(
//                    array(
//                        'recipient' => array(
//                            'id' => $sender,
//                        ),
//                        'message' => $json
//                    )
//                );
//
//                $this->CurlFunction($jsonData, $url);