<div style="padding:10px; border:1px solid #ccc; float:left; clear:both;">
      <label>
         <span class="title"><?php _e( 'Min Preparation', 'pi-edd' ); ?></span>
         <span class="input-text-wrap">
         <input type="number" class="form-control" style="" name="product_preparation_time" id="product_preparation_time" placeholder="0" step="1" min="0">
         </span>
      </label>

      <label>
         <span class="title"><?php _e( 'Max Preparation', 'pi-edd' ); ?></span>
         <span class="input-text-wrap">
         <input type="number" class="form-control" style="" name="product_preparation_time_max" id="product_preparation_time_max"  placeholder="0" step="1" min="0">
         </span>
      </label>
</div>

<div class="pi-edd-set-backorder-bulk" style="padding:10px; border:1px solid #ccc; float:left; clear:both;">
<label><input type="checkbox" value="1" name="set_back_order_days_bulk" id="set_back_order_days_bulk"> Set back order product preparation days</label>
<div class="backorder_days_settin_from_bulk" style="display:none;">

   <label>
         <span class="title"><?php _e( 'Extra time as', 'pi-edd' ); ?></span>
         <span class="input-text-wrap">
         <select name="pisol_edd_extra_time_as" id="pi-extra-time-type-bulk">
			<option value="single"><?php _e( 'Single time', 'pi-edd' ); ?></option>
         <option value="range"><?php _e( 'Range of time', 'pi-edd' ); ?></option>		
         </select>
         </span>
   </label>

   <label id="single-time-bulk">
         <span class="title"><?php _e( 'Extra days', 'pi-edd' ); ?></span>
         <span class="input-text-wrap">
         <input type="number" class="form-control" style="" name="out_of_stock_product_preparation_time" id="out_of_stock_product_preparation_time" placeholder="If left blank 0 will be considered" step="1" min="0">
         </span>
   </label>

   <label class="range-time-bulk">
         <span class="title"><?php _e( 'Min Extra days', 'pi-edd' ); ?></span>
         <span class="input-text-wrap">
         <input type="number" class="form-control" style="" name="out_of_stock_product_preparation_time_min" placeholder="If left blank 0 will be considered" step="1" min="0">
         </span>
   </label>

   <label class="range-time-bulk">
         <span class="title"><?php _e( 'Max Extra days', 'pi-edd' ); ?></span>
         <span class="input-text-wrap">
         <input type="number" class="form-control" style="" name="out_of_stock_product_preparation_time_max" placeholder="If left blank 0 will be considered" step="1" min="0">
         </span>
   </label>

   
</div>
</div>