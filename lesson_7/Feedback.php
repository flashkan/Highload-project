<?php

class Feedback
{
    /**
     * Get oll orders and put on line 'feedback' test comment
     * @param string $comment
     */
    public static function clientFeedback( $comment)
    {
        $result = file_get_contents('archive.json');
        if ($result) {
            $result = json_decode($result, true);
            foreach ($result as &$item) {
                // Клиенты пишут свои комментарии.
                $item['feedback'] = $comment;
                sleep(1);
            }
            file_put_contents('archive.json', json_encode($result));
        }
    }
}


Feedback::clientFeedback((string) 'some comment');