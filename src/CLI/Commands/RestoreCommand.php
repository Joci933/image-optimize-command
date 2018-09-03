<?php

declare(strict_types=1);

namespace TypistTech\ImageOptimizeCommand\CLI\Commands;

use Symfony\Component\Filesystem\Filesystem;
use TypistTech\ImageOptimizeCommand\CLI\LoggerFactory;
use TypistTech\ImageOptimizeCommand\Operations\AttachmentImages\Restore;
use TypistTech\ImageOptimizeCommand\Repositories\AttachmentRepository;
use WP_CLI;

class RestoreCommand
{
    /**
     * Restore the full sized images of specific attachments.
     *
     * ## OPTIONS
     *
     * <id>...
     * : The IDs of attachments to optimize.
     *
     * [--yes]
     * : Answer yes to the confirmation message.
     *
     * ## EXAMPLES
     *
     *     # Optimize attachment ID: 123
     *     $ wp image-optimize restore 123
     *
     *     # Optimize multiple restore attachments
     *     $ wp image-optimize restore 123 223 323
     */
    public function __invoke($args, $assocArgs): void
    {
        $ids = array_map(function (string $id): ?int {
            return is_numeric($id)
                ? (int) $id
                : null;
        }, $args);
        $ids = array_filter($ids);

        $logger = LoggerFactory::create();

        $logger->warning('You have to regenerate the thumbnails afterwards');
        $logger->warning('by running the following command:');
        $logger->warning('$ wp media regenerate ' . implode(' ', $ids));
        WP_CLI::confirm('Are you sure you want to restore the original full sized images?', $assocArgs);

        $repo = new AttachmentRepository();
        $filesystem = new Filesystem();

        $restoreOperation = new Restore($repo, $filesystem, $logger);
        $restoreOperation->execute(...$ids);
    }
}
