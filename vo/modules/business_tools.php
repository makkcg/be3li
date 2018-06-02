<?php
$html_page->writeHeader();
$html_page->writeBody("Business Tools",$core->is_bu_qualified($_SESSION['ir_id'],'001',$database_manager));

?>


<table class="table table-striped">
    <thead>
        <tr>
            <th class="product-column">Tool</th>
            <th class="price-column">Action</th>
        </tr>
    </thead>
    <tbody>
            <tr>
                <td class="product-column">Business Card</td>
                <td class="price-column"><a href='btools/BizCard_Template.jpg' target="_blank">Download</a></td>
            </tr>
            <tr>
                <td class="product-column">The name of the document</td>
                <td class="price-column"><a href=''>Download</a></td>
            </tr>
            <tr>
                <td class="product-column">The name of the document</td>
                <td class="price-column"><a href=''>Download</a></td>
            </tr>
            <tr>
                <td class="product-column">The name of the document</td>
                <td class="price-column"><a href=''>Download</a></td>
            </tr>
            <tr>
                <td class="product-column">The name of the document</td>
                <td class="price-column"><a href=''>Download</a></td>
            </tr>
    </tbody>
</table>

<?php $html_page->writeFooter(); ?>