<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer->run("
    ALTER TABLE {$this->getTable('blog/blog')} ADD `image_main` smallint( 6 ) NOT NULL default '0';
    ALTER TABLE {$this->getTable('blog/blog')} ADD `image` varchar( 255 ) NOT NULL default '';
    ALTER TABLE {$this->getTable('blog/blog')} ADD `image_retina` varchar( 255 ) NOT NULL default '';
    ALTER TABLE {$this->getTable('blog/blog')} ADD `thumb` varchar( 255 ) NOT NULL default '';
    ALTER TABLE {$this->getTable('blog/blog')} ADD `thumb_retina` varchar( 255 ) NOT NULL default '';
");

//update default config
$installer->setConfigData('blog/blog/layout', 'page/2columns-right.phtml');
$installer->setConfigData('blog/blog/parse_cms', '1');
$installer->setConfigData('blog/blog/perpage', '10');
$installer->setConfigData('blog/blog/blogcrumbs', '1');
$installer->setConfigData('blog/menu/right', '2');
$installer->setConfigData('blog/menu/top', '1');
$installer->setConfigData('blog/menu/recent', '3');
$installer->setConfigData('blog/menu/category', '1');

try {
    $post = Mage::getModel('blog/post')->loadByIdentifier('Hello');

    $post->setData('identifier', 'athlete-typography');
    $post->setData('title', 'Athlete typography');
    $post->setData('status', '1');

    $post->setData('created_time', Mage::getModel('core/date')->gmtDate());
    $post->setData('update_time', null);
    $post->setData('user', 'athlete');
    $post->setData('update_user', 'athlete');

    $post->setData('meta_keywords', 'Athlete magento theme');
    $post->setData('meta_description', 'Athlete magento theme');

	$post->setData('short_content', '<h1>H1 tag example</h1>
<p>sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure</p>');
	
    $post->setData('post_content', '
<h1>H1 tag example</h1>
<p>sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure</p>
<h2>H2 tag example</h2>
<p>sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure</p>
<h3>H3 tag example</h3>
<p>sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure</p>
<h4>H4 tag example</h4>
<p>sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure</p>
<h5>H5 tag example</h5>
<p>sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure</p>
<h6>H6 tag example</h6>
<p>sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure</p>
<h2>Blockquotes</h2>
<div class="one_half">
<blockquote>
<p>Labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum abore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatu</p>
</blockquote>
<p style="text-align: right;"><a href="//olegnax.com">John Doe</a><br /> Creative Director</p>
</div>
<div class="one_half last">
<blockquote>
<h3>John Doe</h3>
<p>Labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit</p>
</blockquote>
</div>
<div class="clearfix">&nbsp;</div>
<h2>Images</h2>
<p><a title="title link" href="//olegnax.com"><img style="float: left;" src="{{media url="olegnax/athlete/sample_banner_220x120.jpg"}}" alt="" width="220" height="120" /></a> sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliqrporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur?</p>
<p><img style="float: right;" title="typo2" src="{{media url="olegnax/athlete/sample_banner_220x120.jpg"}}" alt="" width="220" height="120" />sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure d quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure</p>
<h2>Lists</h2>
<div class="one_half ">
<h3>Marked</h3>
<ul class="ox_list_simple">
<li>Curabitur eu sapien eget tortor blandit pretium auctor ac metus.</li>
<li>Etiam quis est non velit facilisis auctor.</li>
<li>Vivamus adipiscing auctor quam, at aliquet diam viverra sed.</li>
<li>Integer a tortor quis purus consectetur luctus sit amet ac elit.</li>
<li>Duis eget odio ut tellus pulvinar gravida in in purus.</li>
<li>Cras interdum faucibus erat, sit amet pretium leo dignissim vel.</li>
</ul>
</div>
<div class="one_half last">
<h3>Ordered</h3>
<ol>
<li>Curabitur eu sapien eget tortor blandit pretium auctor ac metus.</li>
<li>Etiam quis est non velit facilisis auctor.</li>
<li>Vivamus adipiscing auctor quam, at aliquet diam viverra sed.</li>
<li>Integer a tortor quis purus consectetur luctus sit amet ac elit.</li>
<li>Duis eget odio ut tellus pulvinar gravida in in purus.</li>
<li>Cras interdum faucibus erat, sit amet pretium leo dignissim vel.</li>
</ol></div>
<div class="clearfix">&nbsp;</div>
<h2>Table</h2>
<table class="ox_table">
<thead>
<tr><th>Header 1</th><th>Header 2</th><th>Header 3</th><th>Header 4</th><th>Header 5</th></tr>
</thead>
<tbody>
<tr>
<td>Division 1</td>
<td>Division 2</td>
<td>Division 3</td>
<td>Division 4</td>
<td>Division 5</td>
</tr>
<tr>
<td>Division 1</td>
<td>Division 2</td>
<td>Division 3</td>
<td>Division 4</td>
<td>Division 5</td>
</tr>
<tr>
<td>Division 1</td>
<td>Division 2</td>
<td>Division 3</td>
<td>Division 4</td>
<td>Division 5</td>
</tr>
<tr>
<td>Division 1</td>
<td>Division 2</td>
<td>Division 3</td>
<td>Division 4</td>
<td>Division 5</td>
</tr>
</tbody>
</table>
<h2>Misc stuff</h2>
<p><sup>sed quia</sup> consequuntur magni dolores <sub>eos qui ratione</sub> voluptatem sequi nesciunt. <cite>Neque porro quisquam est, qui dolorem</cite> ipsum quia dolor <abbr title="Simplicity WordPress Theme">AWT</abbr>sit amet, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam <span class="hdark">consectetur, adipisci velit</span> quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem <abbr title="Simplicity WordPress Theme">AWT</abbr>, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure</p>
<h2>Code</h2>
<pre><code>$productCollection = Mage::getResourceModel(\'catalog/product_collection\');
$layer->prepareProductCollection($productCollection);
$productCollection->addCountToCategories($categories);
return $categories;
</code></pre>
');

    $cats = Mage::getModel('blog/cat')->getCollection();
    foreach ($cats as $cat) {
        if ($cat->getIdentifier() == 'news') {
            $post->setData('cats', array($cat->getId()));
            break;
        }
    }

    $post->save();
} catch (Exception $e) {
    Mage::logException($e);
}


$installer->endSetup();