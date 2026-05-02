<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<rss version="2.0">
<channel>
    <title>Хакатонщик — Новости</title>
    <link>{{ url('/news') }}</link>
    <description>Новости и обновления платформы Хакатонщик</description>
    <language>ru-ru</language>
    @foreach ($posts as $post)
        <item>
            <title>{{ $post->title }}</title>
            <link>{{ route('news.show', $post) }}</link>
            <guid>{{ route('news.show', $post) }}</guid>
            <description><![CDATA[{{ $post->excerpt }}]]></description>
            <pubDate>{{ optional($post->published_at)->toRssString() }}</pubDate>
        </item>
    @endforeach
</channel>
</rss>
