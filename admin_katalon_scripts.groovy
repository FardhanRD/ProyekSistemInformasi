import static com.kms.katalon.core.testobject.ObjectRepository.findTestObject
import com.kms.katalon.core.webui.keyword.WebUiBuiltInKeywords as WebUI
import com.kms.katalon.core.model.FailureHandling as FailureHandling
import com.kms.katalon.core.testobject.TestObject as TestObject
import com.kms.katalon.core.testobject.ConditionType as ConditionType


/*
================================================================================
KATALON STUDIO AUTOMATION TEST SCRIPTS: MOVR ADMIN PANEL
This file contains the complete, copy-pasteable script suite for all 36 test cases.
================================================================================
*/

class MovrAdminTests {

    // ==========================================
    // MODULE 1: Session & Authentication
    // ==========================================

    static void TC_AUTH_01_LoginSuccess() {
        WebUI.openBrowser('http://127.0.0.1:8000/login')
        WebUI.maximizeWindow()
        WebUI.setText(findTestObject('Object Repository/Login/input_Email'), 'admin.demo@movr.test')
        WebUI.setText(findTestObject('Object Repository/Login/input_Password'), 'admin123')
        WebUI.click(findTestObject('Object Repository/Login/button_Submit'))
        WebUI.verifyElementPresent(findTestObject('Object Repository/Sidebar/menu_Dashboard'), 5)
    }

    static void TC_AUTH_02_LoginFailed() {
        WebUI.openBrowser('http://127.0.0.1:8000/login')
        WebUI.setText(findTestObject('Object Repository/Login/input_Email'), 'admin.demo@movr.test')
        WebUI.setText(findTestObject('Object Repository/Login/input_Password'), 'wrongpassword')
        WebUI.click(findTestObject('Object Repository/Login/button_Submit'))
        WebUI.verifyTextPresent('Credentials do not match', false)
    }

    static void TC_AUTH_03_Logout() {
        WebUI.click(findTestObject('Object Repository/Sidebar/button_SignOut'))
        WebUI.verifyElementPresent(findTestObject('Object Repository/Login/input_Email'), 5)
        WebUI.closeBrowser()
    }

    // ==========================================
    // MODULE 2: Dashboard & Notifications
    // ==========================================

    static void TC_DSH_01_VerifyDashboardMetrics() {
        WebUI.navigateToUrl('http://127.0.0.1:8000/admin/dashboard')
        WebUI.verifyElementVisible(findTestObject('Object Repository/Dashboard/widget_Revenue'))
        WebUI.verifyElementVisible(findTestObject('Object Repository/Dashboard/widget_ActiveUsers'))
        WebUI.verifyElementVisible(findTestObject('Object Repository/Dashboard/widget_ProductsSold'))
        WebUI.verifyElementVisible(findTestObject('Object Repository/Dashboard/widget_TotalOrders'))
    }

    static void TC_DSH_02_ExportDashboardAnalytics() {
        WebUI.navigateToUrl('http://127.0.0.1:8000/admin/dashboard')
        WebUI.click(findTestObject('Object Repository/Dashboard/button_ExportAnalytics'))
        // Download verification can be done via browser downloads path
    }

    static void TC_NTF_01_MarkNotificationsRead() {
        WebUI.navigateToUrl('http://127.0.0.1:8000/admin/dashboard')
        WebUI.click(findTestObject('Object Repository/Dashboard/button_NotificationBell'))
        WebUI.click(findTestObject('Object Repository/Notifications/button_ReadAll'))
        WebUI.verifyElementNotPresent(findTestObject('Object Repository/Notifications/badge_UnreadCount'), 3)
    }

    // ==========================================
    // MODULE 3: Category & Supplier Management
    // ==========================================

    static void TC_MST_01_CreateParentCategory() {
        WebUI.navigateToUrl('http://127.0.0.1:8000/admin/category')
        WebUI.click(findTestObject('Object Repository/Category/button_TambahKategori'))
        WebUI.setText(findTestObject('Object Repository/Category/input_NamaKategori'), 'Sports Equipment')
        WebUI.selectOptionByValue(findTestObject('Object Repository/Category/select_ParentCategory'), '', false)
        WebUI.click(findTestObject('Object Repository/Category/button_Simpan'))
        WebUI.verifyTextPresent('Sports Equipment', false)
    }

    static void TC_MST_02_CreateSubCategory() {
        WebUI.navigateToUrl('http://127.0.0.1:8000/admin/category')
        WebUI.click(findTestObject('Object Repository/Category/button_TambahKategori'))
        WebUI.setText(findTestObject('Object Repository/Category/input_NamaKategori'), 'Rackets')
        // Select 'Sports Equipment' value (assume ID 10 for sports equipment)
        WebUI.selectOptionByLabel(findTestObject('Object Repository/Category/select_ParentCategory'), 'Sports Equipment', false)
        WebUI.click(findTestObject('Object Repository/Category/button_Simpan'))
        WebUI.verifyTextPresent('Rackets', false)
    }

    static void TC_MST_03_EditCategory() {
        WebUI.navigateToUrl('http://127.0.0.1:8000/admin/category')
        WebUI.click(findTestObject('Object Repository/Category/button_Edit_Rackets'))
        WebUI.setText(findTestObject('Object Repository/Category/input_NamaKategori'), 'Tennis Rackets')
        WebUI.click(findTestObject('Object Repository/Category/button_Update'))
        WebUI.verifyTextPresent('Tennis Rackets', false)
    }

    static void TC_MST_04_DeleteCategory() {
        WebUI.navigateToUrl('http://127.0.0.1:8000/admin/category')
        WebUI.click(findTestObject('Object Repository/Category/button_Delete_TennisRackets'))
        WebUI.acceptAlert()
        WebUI.verifyTextNotPresent('Tennis Rackets', false)
    }

    static void TC_MST_05_AddSupplierPartner() {
        WebUI.navigateToUrl('http://127.0.0.1:8000/admin/supplier')
        WebUI.click(findTestObject('Object Repository/Supplier/button_TambahSupplier'))
        WebUI.setText(findTestObject('Object Repository/Supplier/input_NamaToko'), 'Eiger Store')
        WebUI.setText(findTestObject('Object Repository/Supplier/input_NamaOwner'), 'Ronny Lukito')
        WebUI.setText(findTestObject('Object Repository/Supplier/input_Email'), 'eiger@supplier.test')
        WebUI.setText(findTestObject('Object Repository/Supplier/textarea_Alamat'), 'Jalan Sumatera No 20 Bandung')
        WebUI.check(findTestObject('Object Repository/Supplier/toggle_Verify'))
        WebUI.click(findTestObject('Object Repository/Supplier/button_Simpan'))
        WebUI.verifyTextPresent('Eiger Store', false)
    }

    static void TC_MST_06_DeleteSupplier() {
        WebUI.navigateToUrl('http://127.0.0.1:8000/admin/supplier')
        WebUI.click(findTestObject('Object Repository/Supplier/button_Delete_EigerStore'))
        WebUI.acceptAlert()
        WebUI.verifyTextNotPresent('Eiger Store', false)
    }

    // ==========================================
    // MODULE 4: Product Catalog & Media
    // ==========================================

    static void TC_PRD_01_CreateProductStep1() {
        WebUI.navigateToUrl('http://127.0.0.1:8000/admin/master-product/create')
        WebUI.setText(findTestObject('Object Repository/Product/input_NamaProduk'), 'Laica Court Skirt')
        WebUI.selectOptionByLabel(findTestObject('Object Repository/Product/select_Category'), 'Women Clothing', false)
        WebUI.selectOptionByLabel(findTestObject('Object Repository/Product/select_Supplier'), 'LAICA', false)
        WebUI.setText(findTestObject('Object Repository/Product/input_HargaDasar'), '300000')
        WebUI.click(findTestObject('Object Repository/Product/button_NextToVariants'))
        WebUI.verifyElementPresent(findTestObject('Object Repository/Product/input_VariantName'), 5)
    }

    static void TC_PRD_02_ConfigureVariantsStep2() {
        WebUI.setText(findTestObject('Object Repository/Product/input_VariantName'), 'Pink Small')
        WebUI.setText(findTestObject('Object Repository/Product/input_VariantSize'), 'S')
        WebUI.setText(findTestObject('Object Repository/Product/input_VariantColor'), 'Pink')
        WebUI.setText(findTestObject('Object Repository/Product/input_VariantStock'), '15')
        WebUI.click(findTestObject('Object Repository/Product/button_NextToMedia'))
        WebUI.verifyElementPresent(findTestObject('Object Repository/Product/input_UploadFile'), 5)
    }

    static void TC_PRD_03_UploadMediaStep3() {
        WebUI.uploadFile(findTestObject('Object Repository/Product/input_UploadFile'), 'C:\\images\\laica_skirt.jpg')
        WebUI.click(findTestObject('Object Repository/Product/button_SaveProduct'))
        WebUI.verifyTextPresent('Laica Court Skirt', false)
    }

    static void TC_PRD_04_EditProductDetails() {
        WebUI.navigateToUrl('http://127.0.0.1:8000/admin/master-product')
        WebUI.click(findTestObject('Object Repository/Product/button_Edit_LaicaCourtSkirt'))
        WebUI.setText(findTestObject('Object Repository/Product/input_NamaProduk'), 'Laica Court Skirt Premium')
        WebUI.click(findTestObject('Object Repository/Product/button_Save'))
        WebUI.verifyTextPresent('Laica Court Skirt Premium', false)
    }

    static void TC_PRD_05_DeactivateProduct() {
        WebUI.navigateToUrl('http://127.0.0.1:8000/admin/master-product')
        WebUI.click(findTestObject('Object Repository/Product/button_Deactivate_LaicaCourtSkirt'))
        WebUI.acceptAlert()
        WebUI.verifyTextPresent('Nonaktif', false)
    }

    static void TC_PRD_06_ExportProductList() {
        WebUI.navigateToUrl('http://127.0.0.1:8000/admin/master-product')
        WebUI.click(findTestObject('Object Repository/Product/button_ExportExcel'))
    }

    static void TC_MED_01_SetVariantThumbnail() {
        WebUI.navigateToUrl('http://127.0.0.1:8000/admin/media')
        WebUI.click(findTestObject('Object Repository/Media/button_SetThumbnail_Image1'))
        WebUI.verifyElementPresent(findTestObject('Object Repository/Media/badge_ThumbnailMark'), 3)
    }

    static void TC_MED_02_DeleteProductImage() {
        WebUI.navigateToUrl('http://127.0.0.1:8000/admin/media')
        WebUI.click(findTestObject('Object Repository/Media/button_Delete_Image2'))
        WebUI.acceptAlert()
        WebUI.verifyElementNotPresent(findTestObject('Object Repository/Media/card_Image2'), 3)
    }

    // ==========================================
    // MODULE 5: Pricing Management
    // ==========================================

    static void TC_PRC_01_UpdateVariantPrice() {
        WebUI.navigateToUrl('http://127.0.0.1:8000/admin/pricing')
        WebUI.click(findTestObject('Object Repository/Pricing/button_Edit_Price_Variant1'))
        WebUI.setText(findTestObject('Object Repository/Pricing/input_Price'), '320000')
        WebUI.click(findTestObject('Object Repository/Pricing/button_SavePrice'))
        WebUI.verifyTextPresent('Rp 320.000', false)
    }

    static void TC_PRC_02_BulkUpdatePrices() {
        WebUI.navigateToUrl('http://127.0.0.1:8000/admin/pricing')
        WebUI.check(findTestObject('Object Repository/Pricing/checkbox_Variant1'))
        WebUI.check(findTestObject('Object Repository/Pricing/checkbox_Variant2'))
        WebUI.click(findTestObject('Object Repository/Pricing/button_BulkEdit'))
        WebUI.setText(findTestObject('Object Repository/Pricing/input_BulkPrice'), '350000')
        WebUI.click(findTestObject('Object Repository/Pricing/button_SaveBulk'))
        WebUI.verifyTextPresent('Rp 350.000', false)
    }

    // ==========================================
    // MODULE 6: Inventory & Purchase Orders (PO)
    // ==========================================

    static void TC_INV_01_CreateSupplierProductLink() {
        WebUI.navigateToUrl('http://127.0.0.1:8000/admin/supplier-product')
        
        TestObject btnAdd = new TestObject("btnAdd")
        btnAdd.addProperty("xpath", ConditionType.EQUALS, "//button[contains(., 'Add Relation')]")
        WebUI.click(btnAdd)
        
        TestObject selSupplier = new TestObject("selSupplier")
        selSupplier.addProperty("xpath", ConditionType.EQUALS, "//select[@name='supplier_id']")
        WebUI.selectOptionByLabel(selSupplier, 'LAICA', false)
        
        TestObject optProduct = new TestObject("optProduct")
        optProduct.addProperty("xpath", ConditionType.EQUALS, "//select[@name='produk_id']/option[contains(text(), 'Laica Court Skirt')]")
        String productVal = WebUI.getAttribute(optProduct, "value")
        
        TestObject selectProduct = new TestObject("select_Product")
        selectProduct.addProperty("xpath", ConditionType.EQUALS, "//select[@name='produk_id']")
        WebUI.selectOptionByValue(selectProduct, productVal, false)
        
        TestObject txtHarga = new TestObject("txtHarga")
        txtHarga.addProperty("xpath", ConditionType.EQUALS, "//input[@name='harga_modal']")
        WebUI.setText(txtHarga, '210000')
        
        TestObject btnSave = new TestObject("btnSave")
        btnSave.addProperty("xpath", ConditionType.EQUALS, "//form[contains(@action, 'supplier-product')]//button[contains(., 'Simpan Relasi')]")
        WebUI.click(btnSave)
        
        WebUI.verifyTextPresent('LAICA', false)
    }

    static void TC_INV_02_UpdateSupplierPriceRedirect() {
        WebUI.navigateToUrl('http://127.0.0.1:8000/admin/supplier-product')
        
        TestObject buttonEdit = new TestObject("buttonEdit")
        buttonEdit.addProperty("xpath", ConditionType.EQUALS, "(//button[contains(., 'Edit')])[1]")
        WebUI.click(buttonEdit)
        WebUI.delay(1)
        
        TestObject inputHargaEdit = new TestObject("inputHargaEdit")
        inputHargaEdit.addProperty("xpath", ConditionType.EQUALS, "//input[@id='editHargaModal']")
        WebUI.setText(inputHargaEdit, '200000')
        
        TestObject buttonUpdate = new TestObject("buttonUpdate")
        buttonUpdate.addProperty("xpath", ConditionType.EQUALS, "//form[@id='editRelationForm']//button[contains(., 'Update Relasi')]")
        WebUI.click(buttonUpdate)
        
        TestObject divSuccess = new TestObject("divSuccess")
        divSuccess.addProperty("xpath", ConditionType.EQUALS, "//div[contains(@class, 'alert-success') or contains(@style, 'background:#DCFCE7') or contains(@style, 'background: rgb(220, 252, 231)')]")
        WebUI.verifyElementPresent(divSuccess, 5)
        WebUI.verifyTextPresent('Relasi supplier-produk berhasil diperbarui.', false)
    }

    static void TC_INV_03_ManualStockAdjustment() {
        WebUI.navigateToUrl('http://127.0.0.1:8000/admin/stock')
        
        TestObject buttonAdjust = new TestObject("buttonAdjust")
        buttonAdjust.addProperty("xpath", ConditionType.EQUALS, "//tr[td[4][not(contains(., '0 pcs'))]]//button[contains(., 'Adjust')]")
        WebUI.click(buttonAdjust)
        WebUI.delay(1)
        
        TestObject inputQty = new TestObject("inputQty")
        inputQty.addProperty("xpath", ConditionType.EQUALS, "//input[@name='qty']")
        WebUI.setText(inputQty, '-2')
        
        TestObject buttonSubmit = new TestObject("buttonSubmit")
        buttonSubmit.addProperty("xpath", ConditionType.EQUALS, "//button[contains(., 'Simpan Penyesuaian') or contains(., 'Submit')]")
        WebUI.click(buttonSubmit)
        
        TestObject divSuccess = new TestObject("divSuccess")
        divSuccess.addProperty("xpath", ConditionType.EQUALS, "//div[contains(@class, 'alert-success') or contains(@style, 'background:#DCFCE7') or contains(@style, 'background: rgb(220, 252, 231)')]")
        WebUI.verifyElementPresent(divSuccess, 5)
    }

    static void TC_INV_04_ExportStockMovement() {
        WebUI.navigateToUrl('http://127.0.0.1:8000/admin/stock-movement')
        WebUI.click(findTestObject('Object Repository/StockMovement/button_ExportLogs'))
    }

    static void TC_INV_05_CreatePOToSupplier() {
        WebUI.navigateToUrl('http://127.0.0.1:8000/admin/supplier-order/create')
        
        TestObject optSupplier = new TestObject("optSupplier")
        optSupplier.addProperty("xpath", ConditionType.EQUALS, "//select[@name='supplier_id']/option[contains(text(), 'LAICA')]")
        String supplierVal = WebUI.getAttribute(optSupplier, "value")

        TestObject selSupplier = new TestObject("selSupplier")
        selSupplier.addProperty("xpath", ConditionType.EQUALS, "//select[@name='supplier_id']")
        WebUI.selectOptionByValue(selSupplier, supplierVal, false)
        
        TestObject btnAdd = new TestObject("btnAdd")
        btnAdd.addProperty("xpath", ConditionType.EQUALS, "//button[contains(., 'Tambah Item PO') or contains(., 'Tambah Baris Baru')]")
        WebUI.click(btnAdd)
        
        TestObject optVariant = new TestObject("optVariant")
        optVariant.addProperty("xpath", ConditionType.EQUALS, "//select[contains(@x-model, 'detail_produk_id')]/option[contains(text(), 'Laica Court Skirt') and (contains(text(), 'Pink') or contains(text(), 'PINK'))]")
        String variantVal = WebUI.getAttribute(optVariant, "value")
        
        TestObject selVariant = new TestObject("selVariant")
        selVariant.addProperty("xpath", ConditionType.EQUALS, "//select[contains(@x-model, 'detail_produk_id')]")
        WebUI.selectOptionByValue(selVariant, variantVal, false)
        
        TestObject txtQty = new TestObject("txtQty")
        txtQty.addProperty("xpath", ConditionType.EQUALS, "//input[@x-model.number='item.qty']")
        WebUI.setText(txtQty, '30')
        
        TestObject txtHarga = new TestObject("txtHarga")
        txtHarga.addProperty("xpath", ConditionType.EQUALS, "//input[@x-model.number='item.harga_beli']")
        WebUI.setText(txtHarga, '200000')
        
        TestObject btnSave = new TestObject("btnSave")
        btnSave.addProperty("xpath", ConditionType.EQUALS, "//button[contains(., 'Simpan PO')]")
        WebUI.click(btnSave)
        
        WebUI.verifyTextPresent('PO-', false)
    }

    static void TC_INV_06_ReceivePOStock() {
        // Open details of the recently created PO
        WebUI.click(findTestObject('Object Repository/PO/button_ViewDetail_RecentPO'))
        WebUI.click(findTestObject('Object Repository/PO/button_TerimaBarang'))
        WebUI.acceptAlert()
        WebUI.verifyTextPresent('PO diterima, stok berhasil diperbarui!', false)
    }

    static void TC_INV_07_DownloadPOInvoicePDF() {
        WebUI.click(findTestObject('Object Repository/PO/button_DownloadPOInvoice'))
    }

    // ==========================================
    // MODULE 7: Customer Orders & Shipping
    // ==========================================

    static void TC_ORD_01_AcceptCustomerOrder() {
        WebUI.navigateToUrl('http://127.0.0.1:8000/admin/customer-order')
        WebUI.click(findTestObject('Object Repository/CustomerOrder/button_ViewDetail_RecentOrder'))
        WebUI.click(findTestObject('Object Repository/CustomerOrder/button_VerifyOrder'))
        WebUI.verifyTextPresent('diproses', false)
    }

    static void TC_ORD_02_VerifyBankTransferPayment() {
        WebUI.navigateToUrl('http://127.0.0.1:8000/admin/customer-order')
        WebUI.click(findTestObject('Object Repository/CustomerOrder/button_ViewDetail_RecentOrder'))
        WebUI.click(findTestObject('Object Repository/CustomerOrder/button_VerifyPaymentProof'))
        WebUI.click(findTestObject('Object Repository/CustomerOrder/button_ApprovePayment'))
        WebUI.verifyTextPresent('Pembayaran berhasil diverifikasi', false)
    }

    static void TC_ORD_03_PackCustomerOrder() {
        WebUI.navigateToUrl('http://127.0.0.1:8000/admin/shipping')
        WebUI.click(findTestObject('Object Repository/Shipping/button_KemasPesanan_Order1'))
        WebUI.verifyTextPresent('Diproses', false)
    }

    static void TC_ORD_04_ShipOrderInputResi() {
        WebUI.navigateToUrl('http://127.0.0.1:8000/admin/shipping')
        WebUI.click(findTestObject('Object Repository/Shipping/button_InputResi_Order1'))
        WebUI.setText(findTestObject('Object Repository/Shipping/input_NoResi'), 'JNE-TRX-998877')
        WebUI.click(findTestObject('Object Repository/Shipping/button_SaveResi'))
        WebUI.verifyTextPresent('Dalam Pengiriman', false)
    }

    static void TC_ORD_05_RejectPaymentCancelOrder() {
        WebUI.navigateToUrl('http://127.0.0.1:8000/admin/customer-order')
        WebUI.click(findTestObject('Object Repository/CustomerOrder/button_ViewDetail_RecentOrder'))
        WebUI.click(findTestObject('Object Repository/CustomerOrder/button_RejectPayment'))
        WebUI.setText(findTestObject('Object Repository/CustomerOrder/input_RejectReason'), 'Bukti transfer buram/tidak valid.')
        WebUI.click(findTestObject('Object Repository/CustomerOrder/button_SubmitReject'))
        WebUI.verifyTextPresent('dibatalkan', false)
    }

    static void TC_ORD_06_DownloadCustomerInvoice() {
        WebUI.navigateToUrl('http://127.0.0.1:8000/admin/customer-order')
        WebUI.click(findTestObject('Object Repository/CustomerOrder/button_ViewDetail_RecentOrder'))
        WebUI.click(findTestObject('Object Repository/CustomerOrder/button_CetakInvoice'))
    }

    // ==========================================
    // MODULE 8: Marketing & Customer Relations
    // ==========================================

    static void TC_MKT_01_CreateShopVoucher() {
        WebUI.navigateToUrl('http://127.0.0.1:8000/admin/promotion')
        WebUI.click(findTestObject('Object Repository/Promotion/button_TambahVoucher'))
        WebUI.setText(findTestObject('Object Repository/Voucher/input_KodeVoucher'), 'SAVE10')
        WebUI.setText(findTestObject('Object Repository/Voucher/input_NamaVoucher'), 'Diskon Spesial 10%')
        WebUI.selectOptionByValue(findTestObject('Object Repository/Voucher/select_JenisDiskon'), 'persen', false)
        WebUI.setText(findTestObject('Object Repository/Voucher/input_NilaiDiskon'), '10')
        WebUI.click(findTestObject('Object Repository/Voucher/button_Simpan'))
        WebUI.verifyTextPresent('SAVE10', false)
    }

    static void TC_MKT_02_CreateProductDiscount() {
        WebUI.navigateToUrl('http://127.0.0.1:8000/admin/promotion')
        WebUI.click(findTestObject('Object Repository/Promotion/tab_DiskonProduk'))
        WebUI.click(findTestObject('Object Repository/Promotion/button_TambahPromo'))
        WebUI.setText(findTestObject('Object Repository/Promo/input_NamaPromo'), 'Flash Sale Awal Bulan')
        WebUI.setText(findTestObject('Object Repository/Promo/input_PersenDiskon'), '30')
        WebUI.check(findTestObject('Object Repository/Promo/checkbox_LaicaSportsCap'))
        WebUI.click(findTestObject('Object Repository/Promo/button_SimpanPromo'))
        // Go back to voucher tab to verify the discount rate renders properly
        WebUI.click(findTestObject('Object Repository/Promotion/tab_Voucher'))
        WebUI.verifyTextPresent('-30%', false) // No fallback -0% issue
    }

    static void TC_MKT_03_CreateFlashSale() {
        WebUI.navigateToUrl('http://127.0.0.1:8000/admin/promotion')
        WebUI.click(findTestObject('Object Repository/Promotion/tab_FlashSale'))
        WebUI.click(findTestObject('Object Repository/Promotion/button_TambahFlashSale'))
        WebUI.setText(findTestObject('Object Repository/Promo/input_NamaPromo'), 'Flash Sale Kilat')
        WebUI.setText(findTestObject('Object Repository/Promo/input_PersenDiskon'), '50')
        WebUI.setText(findTestObject('Object Repository/Promo/input_StokFlashSale'), '5')
        WebUI.click(findTestObject('Object Repository/Promo/button_Simpan'))
    }

    static void TC_MKT_04_ReplyToReview() {
        WebUI.navigateToUrl('http://127.0.0.1:8000/admin/review')
        WebUI.click(findTestObject('Object Repository/Review/button_Balas_Review1'))
        WebUI.setText(findTestObject('Object Repository/Review/textarea_Komentar'), 'Terima kasih banyak atas feedback Anda!')
        WebUI.click(findTestObject('Object Repository/Review/button_KirimBalasan'))
        WebUI.verifyTextPresent('Terima kasih banyak', false)
    }

    static void TC_MKT_05_DeleteInappropriateReview() {
        WebUI.navigateToUrl('http://127.0.0.1:8000/admin/review')
        WebUI.click(findTestObject('Object Repository/Review/button_Delete_Review2'))
        WebUI.acceptAlert()
        WebUI.verifyElementNotPresent(findTestObject('Object Repository/Review/card_Review2'), 3)
    }

    static void TC_MKT_06_BlockCustomerAccount() {
        WebUI.navigateToUrl('http://127.0.0.1:8000/admin/customer')
        WebUI.click(findTestObject('Object Repository/Customer/button_Block_User1'))
        WebUI.acceptAlert()
        WebUI.verifyTextPresent('Blocked', false)
    }

    // ==========================================
    // MODULE 9: Reports & Audit Logs
    // ==========================================

    static void TC_RPT_01_ViewAuditLogs() {
        WebUI.navigateToUrl('http://127.0.0.1:8000/admin/audit-log')
        WebUI.verifyElementPresent(findTestObject('Object Repository/AuditLog/table_AuditLogs'), 5)
    }

    static void TC_RPT_02_ExportSalesReport() {
        WebUI.navigateToUrl('http://127.0.0.1:8000/admin/report')
        WebUI.setText(findTestObject('Object Repository/Report/input_StartDate'), '2026-06-01')
        WebUI.setText(findTestObject('Object Repository/Report/input_EndDate'), '2026-06-10')
        WebUI.click(findTestObject('Object Repository/Report/button_ExportExcel'))
    }
}
