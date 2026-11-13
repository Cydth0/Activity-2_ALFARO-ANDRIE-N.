<?php
?>

<label for="name">Product name</label>
<input id="name" name="name" type="text" maxlength="100" value="<?=e($product['name'] ?? '')?>" required>

<label for="sku">SKU / Item code <span class="optional">(optional)</span></label>
<input id="sku" name="sku" type="text" maxlength="50" value="<?=e($product['sku'] ?? '')?>" placeholder="e.g. NS-001">

<label for="category">Category</label>
<input id="category" name="category" type="text" maxlength="60" value="<?=e($product['category'] ?? '')?>" placeholder="e.g. Office supplies">

<div class="form-grid">
    <div>
        <label for="quantity">Quantity</label>
        <input id="quantity" name="quantity" type="number" min="0" max="1000000" value="<?=e($product['quantity'] ?? 0)?>" required>
    </div>

    <div>
        <label for="price">Unit price (₱)</label>
        <input id="price" name="price" type="number" min="0" step="0.01" value="<?=e($product['price'] ?? '0.00')?>" required>
    </div>
</div>

<label for="description">Description <span class="optional">(optional)</span></label>
<textarea id="description" name="description" rows="4" maxlength="1000" placeholder="Add a short description..."><?=e($product['description'] ?? '')?></textarea>

<div class="form-buttons">
    <button class="button" type="submit"><?=e($buttonLabel)?></button>
    <a class="button ghost-button" href="products.php">Cancel</a>
</div>