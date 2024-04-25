import { Component, EventEmitter, Input, OnInit, Output, SimpleChange } from '@angular/core';
import * as ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import { PromoService } from '../../services/promo.service';
import { LandaService } from 'src/app/core/services/landa.service';

@Component({
  selector: 'app-form-promo',
  templateUrl: './form-promo.component.html',
  styleUrls: ['./form-promo.component.scss']
})
export class FormPromoComponent implements OnInit {
  readonly DEFAULT_STATUS = 'Voucher';
  // readonly DEFAULT_TYPE = 'Toping';
  readonly MODE_CREATE = 'add';
  readonly MODE_UPDATE = 'update';

  @Input() promoId: string;
  @Output() afterSave = new EventEmitter<boolean>();

  configEditor = ClassicEditor;
  activeMode: string;
  showLoading: boolean;
  formModel: {
    id: string,
    name: string,
    status: string,
    expired_in_day: string,
    nominal_percentage: string,
    nominal_rupiah: string,
    term_conditions: string,
    photo: string,
    photo_url: string,
  }

  constructor(
    private promoService: PromoService,
    private landaService: LandaService,
  ) { }

  ngOnInit(): void { }

  ngOnChanges(changes: SimpleChange) {
    this.resetForm();
  }

  getCroppedImage($event) {
    this.formModel.photo = $event;
  }

  status(){
    if(this.formModel.status == 'Diskon'){
      this.formModel.nominal_rupiah = '';
    }else{
      this.formModel.nominal_percentage = '';
    }
  }

  resetForm() {
    this.formModel = {
      id: '',
      name: '',
      status: this.DEFAULT_STATUS,
      expired_in_day: '',
      nominal_percentage: '',
      nominal_rupiah: '',
      term_conditions: '',
      photo: '',
      photo_url: ''
    }

    if (this.promoId != '') {
      this.activeMode = this.MODE_UPDATE;
      this.getPromo(this.promoId);
      return true;
    }

    this.activeMode = this.MODE_CREATE;
  }

  getPromo(promoId) {
    this.promoService.getPromoId(promoId).subscribe((res: any) => {
      this.formModel = res.data;
    }, err => {
      console.log(err);
    });
  }

  save() {
    switch (this.activeMode) {
      case this.MODE_CREATE:
        this.insert();
        break;
      case this.MODE_UPDATE:
        this.update();
        break;
    }
  }

  insert() {
    this.promoService.createPromo(this.formModel).subscribe((res: any) => {
      this.landaService.alertSuccess('Berhasil', res.message);
      this.afterSave.emit();
    }, err => {
      this.landaService.alertError('Mohon Maaf', err.error.errors);
    });
  }

  update() {
    this.promoService.updatePromo(this.formModel).subscribe((res: any) => {
      this.landaService.alertSuccess('Berhasil', res.message);
      this.afterSave.emit();
    }, err => {
      this.landaService.alertError('Mohon Maaf', err.error.errors);
    });
  }

}
