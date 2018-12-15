<?php
namespace app\api\model;


class CommentTag extends BaseModel
{
    protected $hidden = ['id', 'talent_id', 'comment_id', 'delete_time'];

    public function talent()
    {
        return $this->belongsTo('talent', 'talent_id');
    }

    public function comment()
    {
        return $this->belongsTo('comment', 'comment_id');
    }

    public function edit(array $data) {
        $field = $field = ['talent_id', 'comment_id', 'content'];
        foreach($data as $key => $val) {
            if(in_array($key, $field)) {
                $this->setattr($key, $val);
            }
        }
        return $this;
    }

    /**
     *
     * @param $tags
     * @param $comment
     * @param $talent
     */
    public function saveAllTags($tags, Comment $comment, Talent $talent) {
        @$tags = json_decode($tags, true);
        if($comment->id && isset($tags[0])) {
            $datas = [];
            foreach($tags as $value) {
                $datas[] = [
                    'comment_id' => $comment->id,
                    'talent_id' => $talent->id,
                    'content' => $value
                ];
            }
            self::saveAll($datas);
        }
    }
}