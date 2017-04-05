<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * This view helper class displays breadcrumbs.
 */
class Certificate extends AbstractHelper
{

    public function displayAsHtml($certificateArray, $is_mini_mode = false)
    {
        ob_start();
        $escapeHtml = $this->getView()->plugin('escapeHtml');

        if ($is_mini_mode)
        {
            ?>
            <h3>
                <a href="<?= $this->view->url('certificates', ['action' => 'view', 'id' => $certificateArray['ISIN']]); ?>">
                    <?= $escapeHtml($certificateArray['Title']); ?>
                </a>    
            </h3>
            <?php
        } else
        {
            echo '<h1>' . $escapeHtml($certificateArray['Title']) . '</h1>';
        }
        ?>
        <p>
            ISIN: <?= $escapeHtml($certificateArray['ISIN']);
            
            echo  $is_mini_mode ? " | " : "<br />";
            ?>
            Type: <?= $escapeHtml($certificateArray['Type']); 
            echo  $is_mini_mode ? " | " : "<br />";
            ?>
            <a href="<?= $this->view->url('certificates', ['action' => 'xmlview', 'id' => $certificateArray['ISIN']]);
        ?>">
                Get XML
            </a>
        </p>
        <p>
            Issuer: <?php echo $escapeHtml($certificateArray['Issuer']); 
            echo  $is_mini_mode ? " | " : "<br />";
            ?> 
            Markets: <?= $escapeHtml($certificateArray['Trading Market']); ?>   
        </p> 
        <?php
        if ($is_mini_mode)
        {
            ?>
            <p>
                <strong>
                    <?= $escapeHtml($certificateArray['Current Price']); ?>
                </strong>
            </p>
            <?php
        }  else
        {
            ?>
            <p>
                Current price:
                <strong>
                    <?= $escapeHtml($certificateArray['Current Price']); ?>
                </strong>
            </p>
            <p>
                Issuing price:
                    <?= $escapeHtml($certificateArray['Issuer Price']); ?>
            </p>
            <?php
            
        }
        

        if ($is_mini_mode)
        {
            ?>
            <p class="documents-header">
                <?= $escapeHtml($certificateArray['Documents Summary']); ?>  
            </p>
            <?php
        } else
        {
            echo "<h4>" . $escapeHtml($certificateArray['Documents Summary']) . "</h4>";
            foreach ($certificateArray['Documents'] as $document):
                ?>
                <p>
                    <a href="<?= $escapeHtml($document['url']) ?>">
                        <?= $escapeHtml($document['filename']) ?> (<?= $escapeHtml($document['type']); ?>)
                    </a>
                    on 
                    <?= $escapeHtml($document['dateCreated']); ?>
                </p>
                
                <hr />

                <?php
            endforeach;
        }
        
        if ($is_mini_mode)
        {
            echo "<hr />";
        }  
        
        return ob_get_clean();
    }

    public function displayAsXml($certificate, $certificateManager)
    {
        return $certificateManager->buildXml($certificate);
    }

}
