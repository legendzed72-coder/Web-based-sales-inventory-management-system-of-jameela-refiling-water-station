<?php
// payroll module placeholder
?>
<div class="content-grid">
    <div class="card" style="grid-column:1/-1;text-align:center;">
        <h3>PAYROLL</h3>
        <form method="post" action="process_payroll.php">
            <div class="payroll-inputs">
                <div class="payroll-box">
                    <label>Name</label>
                    <input type="text" name="employee_name" required />
                </div>
                <div class="payroll-box">
                    <label>Gallon Qty</label>
                    <input type="number" name="gallon_qty" value="0" min="0" required />
                </div>
                <div class="payroll-box">
                    <label>Commission per Gallon</label>
                    <input type="number" step="0.01" name="commission_per" value="0" min="0" required />
                </div>
                <div class="payroll-box">
                    <label>Total Commission</label>
                    <input type="number" step="0.01" name="total_commission" value="0" min="0" />
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Save Payroll</button>
        </form>
    </div>
</div>