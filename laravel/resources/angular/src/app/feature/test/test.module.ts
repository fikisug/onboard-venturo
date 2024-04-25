import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { TestDirectiveComponent } from './components/test-directive/test-directive.component';
import { FormsModule } from '@angular/forms';
import { ItemDetailComponent } from './components/item-detail/item-detail.component';



@NgModule({
  declarations: [
    // TestDirectiveComponent,
    // ItemDetailComponent,
  ],
  imports: [
    CommonModule,
    FormsModule,
  ]
})
export class TestModule { }
