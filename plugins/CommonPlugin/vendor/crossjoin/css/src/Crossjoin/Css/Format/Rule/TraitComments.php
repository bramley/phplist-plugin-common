<?php
namespace Crossjoin\Css\Format\Rule;

use Crossjoin\Css\Helper\Placeholder;

trait TraitComments
{
    /**
     * @var array Comment array
     */
    protected $comments = [];

    /**
     * Sets comments.
     *
     * @param string[]|string $comments
     * @return $this
     */
    public function setComments($comments)
    {
        $this->comments = [];
        if (!is_array($comments)) {
            $comments = [$comments];
        }
        foreach ($comments as $comment) {
            $this->addComment($comment);
        }

        return $this;
    }

    /**
     * Adds a comment.
     *
     * @param string $comment
     * @return $this
     */
    public function addComment($comment)
    {
        if (is_string($comment)) {
            $comment = Placeholder::replaceCommentPlaceholders($comment, true);

            $this->comments[] = $comment;
        } else {
            throw new \InvalidArgumentException(
                "Invalid type '" . gettype($comment). "' for argument 'comment' given."
            );
        }

        return $this;
    }

    /**
     * Gets an array of comments.
     *
     * @return string[]
     */
    public function getComments()
    {
        return $this->comments;
    }
}