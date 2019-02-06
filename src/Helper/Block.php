<?php
namespace XanUtility\Helper;

use XanUtility\Application\StaticApplicationTrait;

class Block
{
    use StaticApplicationTrait;

    public static function refreshBlocksOutputCache(array $btHandles)
    {
        if (empty($btHandles)) {
            return;
        }

        $db = self::app()->make('database/connection');
        $subQb = $db->createQueryBuilder();
        $subQb->select('bID')->from('Blocks', 'b')
            ->innerJoin('b', 'BlockTypes', 'bt', 'b.btID = bt.btID')
            ->where($subQb->expr()->in('bt.btHandle', array_map([$db, 'quote'], $btHandles)));

        $qb = $db->createQueryBuilder();
        $qb->update('CollectionVersionBlocksOutputCache', 'cvboc')
            ->join('cvboc', 'Blocks', 'b', 'cvboc.bID = b.bID')
            ->join('b', 'BlockTypes', 'bt', 'b.btID = bt.btID')
            ->set('btCachedBlockOutputExpires', 0)
            ->where('cvboc.bID IN (' . $subQb->getSQL() . ')')
            ->execute();
    }
}
