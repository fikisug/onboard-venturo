import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ListSaleComponent } from './components/list-sale/list-sale.component';
import { FormSaleComponent } from './components/form-sale/form-sale.component';
import { NgSelectModule } from '@ng-select/ng-select';
import { FormsModule } from '@angular/forms';
import { CarouselComponent, CarouselModule } from 'ngx-owl-carousel-o';
import { ProductModule } from '../product/product.module';
import { NgxSpinnerModule } from 'ngx-spinner';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { CoreModule } from 'src/app/core/core.module';
import { CustomerModule } from '../customer/customer.module';
import { CdkDrag, CdkDropList } from '@angular/cdk/drag-drop';

@NgModule({
  declarations: [
    ListSaleComponent,
    FormSaleComponent,
  ],
  imports: [
    CommonModule,
    NgSelectModule,
    FormsModule,
    CarouselModule,
    ProductModule,
    CustomerModule,
    NgxSpinnerModule,
    NgbModule,
    CoreModule,
    CdkDropList,
    CdkDrag
  ]
})
export class SaleModule { }
