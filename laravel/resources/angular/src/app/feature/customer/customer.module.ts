import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormCustomerComponent } from './components/form-customer/form-customer.component';
import { ListCustomerComponent } from './components/list-customer/list-customer.component';
import { FormsModule } from '@angular/forms';
import { DataTablesModule } from 'angular-datatables';
import { SharedModule } from 'src/app/shared/shared.module';
import { NgProgressModule } from 'ngx-progressbar';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';



@NgModule({
  declarations: [
    FormCustomerComponent,
    ListCustomerComponent
  ],
  imports: [
    CommonModule,
    FormsModule,
    DataTablesModule,
    SharedModule,
    NgProgressModule,
    NgbModule
  ],
  exports: [
    FormCustomerComponent
  ]
})
export class CustomerModule { }
