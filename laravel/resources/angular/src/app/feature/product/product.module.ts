import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { DataTablesModule } from 'angular-datatables';
import { NgSelectModule } from '@ng-select/ng-select';
import { SharedModule } from '../../shared/shared.module';
import { CoreModule } from '../../core/core.module';
import { CKEditorModule } from '@ckeditor/ckeditor5-angular';
import { DndModule } from 'ngx-drag-drop';
import { ListProductComponent } from './product/components/list-product/list-product.component';
import { ListCategoryComponent } from './category/components/list-category/list-category.component';
import { FormCategoryComponent } from './category/components/form-category/form-category.component';
import { FormProductComponent } from './product/components/form-product/form-product.component';

@NgModule({
  declarations: [ListProductComponent, FormProductComponent, FormCategoryComponent, ListCategoryComponent, FormCategoryComponent, FormProductComponent],
  imports: [
    CommonModule,
    FormsModule,
    NgbModule,
    DataTablesModule,
    NgSelectModule,
    SharedModule,
    CoreModule,
    CKEditorModule,
    DndModule
  ],exports: [
    FormProductComponent
  ]
})
export class ProductModule { }
