<?php declare(strict_types = 1);

namespace App\Adapters;

use App\Models\Rss\Feed;
use App\Models\Rss\Item;
use Feeds;

class SimplePieAdapter implements AdapterInterface
{
    /** @var \SimplePie */
    private $simplePie;

    public function __construct(\App\Models\Feed $feed)
    {
        $this->simplePie = Feeds::make($feed->getUrl());
    }

    public function getFeed(): Feed
    {
        $items = collect();

        foreach ($this->simplePie->get_items() as $pieItem) {
            $post = $this->createPostFromItem($pieItem);
            $items->push($post);
        }

        return new Feed($this->simplePie->feed_url, $items);
    }

    private function createPostFromItem(\SimplePie_Item $item): Item
    {
        return (new Item())
            ->setTitle($item->get_title())
            ->setContent($item->get_content())
            ->setDescription($item->get_description())
            ->setDate($item->get_date())
            ->setPermalink($item->get_permalink());
    }
}