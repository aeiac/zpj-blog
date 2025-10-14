<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use League\CLImate\CLImate;

class V2EX extends Command
{
    protected $signature = 'app:v2ex';
    protected $description = '抓取 V2EX 最新主题列表并查看正文与回复';

    public function handle()
    {
        // ✅ 清屏（跨平台）
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            system('cls');
        } else {
            system('clear');
        }

        $cli = new CLImate();
        $cli->info("开始抓取 V2EX 最新主题列表...\n");

        try {
            $url = 'https://www.v2ex.com/';
            $client = new Client(['timeout' => 10, 'verify' => false]);
            $response = $client->get($url);
            $html = $response->getBody()->getContents();

            $crawler = new Crawler($html);
            $topics = [];

            // 抓取主题列表
            $crawler->filter('div.cell.item')->each(function (Crawler $node) use (&$topics) {
                $titleNode = $node->filter('span.item_title a.topic-link');
                if ($titleNode->count() == 0) return;

                $title = trim($titleNode->text());
                $href = 'https://www.v2ex.com' . $titleNode->attr('href');

                $nodeLinkNode = $node->filter('span.topic_info a.node');
                $nodeName = $nodeLinkNode->count() > 0 ? trim($nodeLinkNode->text()) : '';

                $topics[] = [
                    '标题' => $title,
                    '链接' => $href,
                    '节点' => $nodeName,
                ];
            });

            if (empty($topics)) {
                $cli->warning("未抓取到任何主题！");
                return;
            }

            // ✅ 表格样式输出（去掉回复数）
            $cli->info(str_repeat('=', 100));
            $cli->info(sprintf("%-6s | %-10s | %-70s", '编号', '节点', '标题'));
            $cli->info(str_repeat('=', 100));

            foreach ($topics as $i => $topic) {
                $title = mb_strimwidth($topic['标题'], 0, 70, '...');
                $node = mb_strimwidth($topic['节点'], 0, 10, ' ');
                $cli->out(sprintf("[%2d]   | %-10s | %-70s", $i, $node, $title));
            }

            $cli->info(str_repeat('=', 100) . "\n");

            // ✅ 选择编号
            $index = (int)$this->ask("请输入要查看的主题编号 (0-" . (count($topics) - 1) . ")");
            if (!isset($topics[$index])) {
                $cli->error("无效的编号！");
                return;
            }

            $topic = $topics[$index];
            $cli->br()->info("正在打开: " . $topic['标题'] . "\n");

            // ✅ 抓取主题详情页
            $response = $client->get($topic['链接']);
            $html = $response->getBody()->getContents();
            $crawler = new Crawler($html);

            // 正文
            $contentNode = $crawler->filter('#Main .markdown_body');
            $content = $contentNode->count() > 0
                ? trim($contentNode->text())
                : '[无正文内容]';

            $cli->br();
            $cli->info("正文：");
            $cli->out($content);
            $cli->br();

            // ✅ 回复（仅内容，不带用户名）
            $replyNodes = $crawler->filter('#Main .reply_content');
            if ($replyNodes->count() > 0) {
                $cli->info(str_repeat('-', 80));
                $cli->info("回复内容：");
                $cli->info(str_repeat('-', 80));

                $replyNodes->each(function (Crawler $rNode, $idx) use ($cli) {
                    $text = trim($rNode->text());
                    if ($text !== '') {
                        $cli->out("[" . ($idx + 1) . "] " . $text);
                    }
                });
            } else {
                $cli->yellow("暂无回复内容。");
            }

        } catch (\Exception $e) {
            $cli->error("抓取失败：" . $e->getMessage());
        }

        $cli->br()->info("\n任务结束。");
    }
}
