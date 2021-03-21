<?php
/**
 * @var Pollen\Partial\PartialViewLoaderInterface $this
 */
?>
<?php echo $this->before(); ?>

<div <?php echo $this->htmlAttrs(); ?>>
    <div data-pdf-viewer="content">
        <?php if ($header = $this->get('content.header')) : ?>
            <div data-pdf-viewer="content.header">
                <?php echo is_string($header) ? $header : $this->fetch('content-header', $this->all()); ?>
            </div>
        <?php endif; ?>

        <div data-pdf-viewer="content.body"><?php $this->insert('content-body', $this->all()); ?></div>

        <?php if ($footer = $this->get('content.footer')) : ?>
            <div data-pdf-viewer="pdfviewer.content.footer">
                <?php echo is_string($footer) ? $footer : $this->fetch('content-footer', $this->all()); ?>
            </div>
        <?php endif; ?>
    </div>

    <div data-pdf-viewer="nav">
        <?php if ($first = $this->get('nav.first')) : ?>
            <span data-pdf-viewer="nav.first">
                <?php echo is_string($first)
                    ? '<button type="button">' . $first .'</button>' : $this->fetch('nav-first', $this->all());
                ?>
            </span>
        <?php endif; ?>

        <?php if ($prev = $this->get('nav.prev')) : ?>
            <span data-pdf-viewer="nav.prev">
                <?php echo is_string($prev)
                    ? '<button type="button">' . $prev .'</button>' : $this->fetch('nav-prev', $this->all());
                ?>
            </span>
        <?php endif; ?>

        <?php if ($next = $this->get('nav.next')) : ?>
            <span data-pdf-viewer="nav.next">
                <?php echo is_string($next)
                    ? '<button type="button">' . $next .'</button>': $this->fetch('nav-next', $this->all());
                ?>
            </span>
        <?php endif; ?>

        <?php if ($last = $this->get('nav.last')) : ?>
            <span data-pdf-viewer="nav.last">
                <?php echo is_string($last)
                    ? '<button type="button">' . $last .'</button>' : $this->fetch('nav-last', $this->all());
                ?>
            </span>
        <?php endif; ?>
    </div>

    <?php /*if ($this->get('page.current') || $this->get('page.total')) : ?>
        <div data-pdf-viewer="page">
            <?php echo $this->insert('page-infos', $this->all()); ?>
        </div>
    <?php endif; */?>

    <div data-pdf-viewer="spinner">
        <?php echo is_string($this->get('spinner')) ? $this->get('spinner') : $this->fetch('spinner', $this->all()); ?>
    </div>
</div>

<?php echo $this->after();