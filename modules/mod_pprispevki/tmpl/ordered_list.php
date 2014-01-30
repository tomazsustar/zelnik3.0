<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<ol>
    <?php foreach ($rows as $row){ ?>
        <li>
            <?php echo JText::sprintf('PRISPEVEK_LABEL', $row->name); ?>
        </li>
  <?php } ?>
</ol>
