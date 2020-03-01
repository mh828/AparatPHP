<?php

namespace MH828\Aparat;

use CURLFile;

class AparatAPI
{
    /**
     * @param Profile $profile
     * @return null|UploadForm
     */
    public function UploadForm(Profile $profile)
    {
        $result = $this->sendRequest("uploadform", [
            'luser' => $profile->username,
            'ltoken' => $profile->ltoken
        ]);
        $std = json_decode($result, true);
        $v = UploadForm::newInstance((object)$std['uploadform']);
        $v->frm_id = $std['uploadform']['frm-id'];

        return $v;
    }

    public function Categories()
    {
        $res = json_decode($this->sendRequest('categories', []));
        return $res->categories;
    }

    public function Video($videohash)
    {
        $res = json_decode($this->sendRequest('video',
            array('videohash' => $videohash)));
        return $res->video;
    }

    /**
     * @param $cat_id
     * @param int $page zero based (start from 0)
     * @param int $perPage
     * @return Pagination
     */
    public function CategoryVideos($cat_id, $page = 0, $perPage = 10)
    {
        $res = json_decode($this->sendRequest('categoryVideos', array(
            'cat' => $cat_id,
            'perpage' => $perPage,
            'curoffset' => $page * $perPage
        )));

        $paginate = new Pagination();
        $paginate->currentPage = $page;
        $paginate->currentOffset = $page * $perPage;
        $paginate->nextOffset = Pagination::fetchOffset($res->ui->pagingForward);
        $paginate->previousOffset = Pagination::fetchOffset($res->ui->pagingBack);
        $paginate->data = $res->categoryvideos;

        return $paginate;
    }

    /**
     * @param $username
     * @param int $page zero based
     * @param int $perPage
     * @return Pagination
     */
    public function VideoByUser($username, $page = 0, $perPage = 10)
    {
        $res = json_decode($this->sendRequest('videoByUser', array(
            'username' => $username,
            'perpage' => $perPage,
            'curoffset' => $perPage * $page
        )));

        $paginate = new Pagination();
        $paginate->currentPage = $page;
        $paginate->currentOffset = $page * $perPage;
        $paginate->nextOffset = Pagination::fetchOffset($res->ui->pagingForward);
        $paginate->previousOffset = Pagination::fetchOffset($res->ui->pagingBack);
        $paginate->data = $res->videobyuser;

        return $paginate;
    }

    public function CommentByVideos($videoHash, $page = 0, $perPage = 10)
    {
        $res = json_decode($this->sendRequest('commentByVideos', array(
            'videohash' => $videoHash,
            'perpage' => $perPage,
            'curoffset' => $perPage * $page
        )));

        $paginate = new Pagination();
        $paginate->currentPage = $page;
        $paginate->currentOffset = $page * $perPage;
        $paginate->nextOffset = Pagination::fetchOffset($res->ui->pagingForward);
        $paginate->previousOffset = Pagination::fetchOffset($res->ui->pagingBack);
        $paginate->data = $res->commentbyvideos;

        return $paginate;
    }

    public function VideoBySearch($text, $page = 0, $perPage = 10){
        $res = json_decode($this->sendRequest('videoBySearch', array(
            'text' => $text,
            'perpage' => $perPage,
            'curoffset' => $perPage * $page
        )));

        $paginate = new Pagination();
        $paginate->currentPage = $page;
        $paginate->currentOffset = $page * $perPage;
        $paginate->nextOffset = Pagination::fetchOffset($res->ui->pagingForward);
        $paginate->previousOffset = Pagination::fetchOffset($res->ui->pagingBack);
        $paginate->data = $res->videobysearch;

        return $paginate;
    }

    /**
     * @param $username
     * @param $password
     * @return  Profile
     */
    public function Login($username, $password)
    {
        $result = json_decode($this->sendRequest("login", [
            'luser' => $username,
            'lpass' => sha1(md5($password))
        ]));

        return Profile::newInstance($result->login);
    }

    /**
     * @param $username
     * @return Profile|null
     */
    public function Profile($username)
    {
        $method = "profile";
        $data = json_decode($this->sendRequest($method, [
            'username' => $username
        ]));
        return Profile::newInstance($data->$method);
    }

    public function userBySearch($text, $page = 0, $perPage = 10, $isTextEncoded = false)
    {
        $res = json_decode($this->sendRequest('userBySearch', [
            'text' => $isTextEncoded ? $text : urlencode($text),
            'perPage' => $perPage,
            'curoffset' => $page * $perPage
        ]));

        $paginate = new Pagination();
        $paginate->currentPage = $page;
        $paginate->currentOffset = $page * $perPage;
        $paginate->nextOffset = Pagination::fetchOffset($res->ui->pagingForward);
        $paginate->previousOffset = Pagination::fetchOffset($res->ui->pagingBack);
        $paginate->data = $res->userbysearch;

        return $paginate;
    }

    public function profileCategories($username)
    {
        $res = json_decode($this->sendRequest("profilecategories", ['username' => $username]));
        return $res->profilecategories;
    }

    /**
     * @param UploadForm $uploadForm
     * @param $video
     * @param $title
     * @param $category
     * @param $tags
     * @param $comment
     * @param null $descr
     * @return object
     */
    public function uploadFile(UploadForm $uploadForm, $video, $title, $category, $tags = '', $comment = true, $descr = null)
    {
        $ch = curl_init();

        $post_data = [
            'video' => new CURLFile($video['tmp_name'], $video['type'], $video['name']),
            'frm-id' => $uploadForm->frm_id,
            'data[title]' => $title,
            'data[category]' => $category,
            'data[tags]' => $tags,
            'data[comment]' => $comment ? 'yes' : 'no',
            'data[descr]' => $descr,
        ];
        if (empty($tags))
            unset($post_data['data[tags]']);

        $options = [
            CURLOPT_URL => $uploadForm->formAction,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $post_data,
            CURLOPT_RETURNTRANSFER => true
        ];

        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($result);
        $result = $result->uploadpost;
        return $result;
    }

    //<editor-fold desc="Protected and Logic">

    /**
     * @param string $method
     * @param array $params Key value pair of params for send to service
     * @param string $HTTPMethod
     * @return bool|string
     */
    protected function sendRequest($method, $params, $HTTPMethod = 'get')
    {
        $ch = curl_init();
        $options = [
            CURLOPT_URL => $this->URLGenerator($method, $params),
            CURLOPT_POST => strtolower($HTTPMethod) == 'post',
            CURLOPT_RETURNTRANSFER => true
        ];

        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * @param $method
     * @param $params
     * @return string
     */
    protected function URLGenerator($method, $params)
    {
        $result = "https://www.aparat.com/etc/api/{$method}/";
        foreach ($params as $k => $v) {
            $result .= "{$k}/{$v}/";
        }

        return $result;
    }
    //</editor-fold>
}