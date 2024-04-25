import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormUserComponent } from './components/form-user/form-user.component';
import { ListUserComponent } from './components/list-user/list-user.component';
import { FormsModule } from '@angular/forms';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { NgProgressModule } from 'ngx-progressbar';
import { DataTablesModule } from 'angular-datatables';
import { SharedModule } from 'src/app/shared/shared.module';
// import { FormProfileComponent } from './components/form-profile/form-profile.component';

@NgModule({
  declarations: [
    FormUserComponent,
    ListUserComponent,
    // FormProfileComponent
  ],
  imports: [
    CommonModule,
    FormsModule,
    DataTablesModule,
    SharedModule,
    NgProgressModule,
    NgbModule,
  ]
})
export class UserModule { }
