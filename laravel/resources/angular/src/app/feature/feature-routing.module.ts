import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DashboardComponent } from './dashboard/dashboard.component';
import { ListUserComponent } from './user/components/list-user/list-user.component';
import { TestDirectiveComponent } from './test/components/test-directive/test-directive.component';
import { ListCustomerComponent } from './customer/components/list-customer/list-customer.component';
import { ListCategoryComponent } from './product/category/components/list-category/list-category.component';
import { ListProductComponent } from './product/product/components/list-product/list-product.component';
import { ListPromoComponent } from './promo/components/list-promo/list-promo.component';
import { ListVoucherComponent } from './promo/voucher/components/list-voucher/list-voucher.component';
import { ListDiscountComponent } from './promo/discount/components/list-discount/list-discount.component';
import { ListSaleComponent } from './sale/components/list-sale/list-sale.component';
import { SalesPromoComponent } from './report/components/sales-promo/sales-promo.component';
import { SaleTransactionComponent } from './report/components/sale-transaction/sale-transaction.component';
import { SalesMenuComponent } from './report/components/sales-menu/sales-menu.component';
import { SaleCustomerComponent } from './report/components/sale-customer/sale-customer.component';

const routes: Routes = [
    { path: '', redirectTo: 'home', pathMatch: 'full' },
    { path: 'home', component: DashboardComponent },
    { path: 'user', component: ListUserComponent },
    { path: 'customer', component: ListCustomerComponent },
    { path: 'test', component: TestDirectiveComponent },
    { path: 'category', component: ListCategoryComponent },
    { path: 'product', component: ListProductComponent },
    { path: 'promo', component: ListPromoComponent },
    { path: 'voucher', component: ListVoucherComponent },
    { path: 'discount', component: ListDiscountComponent },
    { path: 'sale', component: ListSaleComponent },
    { path: 'report/sales-promo', component: SalesPromoComponent },
    { path: 'report/sales-transaction', component: SaleTransactionComponent },
    { path: 'report/sales-menu', component: SalesMenuComponent },
    { path: 'report/sales-customer', component: SaleCustomerComponent },
];

@NgModule({
    imports: [RouterModule.forChild(routes)],
    exports: [RouterModule]
})
export class FeatureRoutingModule { }
