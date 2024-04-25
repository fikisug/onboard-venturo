import { NgModule } from '@angular/core';
import { CommonModule, NgOptimizedImage } from '@angular/common';
import { PageTitleComponent } from './page-title/page-title.component';
import { FormsModule } from '@angular/forms';
import { ImageCropperModule } from 'ngx-image-cropper';
import { Daterangepicker } from 'ng2-daterangepicker';
import { DaterangepickerComponent } from './daterangepicker/daterangepicker.component';
import { UploadImageComponent } from './upload-image/upload-image.component';

@NgModule({
  declarations: [PageTitleComponent, DaterangepickerComponent, UploadImageComponent],
  imports: [
    CommonModule,
    FormsModule,
    ImageCropperModule,
    Daterangepicker,
    NgOptimizedImage
  ],
  exports: [
    PageTitleComponent,
    DaterangepickerComponent,
    UploadImageComponent
  ]
})
export class SharedModule { }
