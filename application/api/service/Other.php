<?php
# Other.php

namespace app\api\service;


class Other extends AbstractServiceModel
{
    public function videoList() {
        $list = [];
        $model = $this->m('Video');
        $param = $this->request->param();
        $since_id = $param['fresh_type'] == 1 ? $param['since_id'] : 0;
        $rows = $model->getList($since_id);

        foreach ($rows as $video) {
            $video->visible(['id', 'title', 'url', 'thumb', 'watch_num', 'create_time']);
            $since_id = (int) $video->id;
        }
        $data = [
            'list' => $rows,
            'count' => count($list),
            'since_id' => $since_id,
        ];
        return $data;
    }
}