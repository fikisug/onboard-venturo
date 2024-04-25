import { Component, EventEmitter, Input, OnInit, Output, SimpleChange } from '@angular/core';
import * as ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import { VoucherService } from '../../services/voucher.service';
import { CustomerService } from 'src/app/feature/customer/services/customer.service';
import { PromoService } from '../../../services/promo.service';
import { LandaService } from 'src/app/core/services/landa.service';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-form-voucher',
  templateUrl: './form-voucher.component.html',
  styleUrls: ['./form-voucher.component.scss']
})
export class FormVoucherComponent implements OnInit {

  readonly PROMO_VOUCHER = 'voucher';
  readonly MODE_CREATE = 'add';
  readonly MODE_UPDATE = 'update';

  @Input() voucherId: string;
  @Output() afterSave = new EventEmitter<boolean>();

  configEditor = ClassicEditor;
  activeMode: string;
  customers: [];
  promo: [];
  showLoading: boolean;
  formModel: {
    id: number,
    period: string,
    customer_id: string,
    promo_id: string,
    start_time: string,
    end_time: string,
    total_voucher: number,
    nominal_rupiah: number,
    photo: string,
    photo_url: string,
    description: string
  }

  constructor(
    private modalService: NgbModal,
    private voucherService: VoucherService,
    private customerService: CustomerService,
    private promoService: PromoService,
    private landaService: LandaService,
  ) { }

  ngOnInit(): void { }

  ngOnChanges(changes: SimpleChange) {
    this.resetForm();
  }

  createCustomer(modalId) {
    this.modalService.open(modalId, { size: 'lg', backdrop: 'static' });
  }

  setPeriodValue($event) {
    this.formModel.start_time = $event.startDate;
    this.formModel.end_time = $event.endDate;
  }

  getCroppedImage($event) {
    this.formModel.photo = $event;
  }

  resetForm() {
    this.getCustomers();
    this.getPromo();
    this.formModel = {
      id: 0,
      period: '',
      customer_id: null,
      promo_id: null,
      start_time: '',
      end_time: '',
      total_voucher: 0,
      nominal_rupiah: 0,
      photo: '',
      photo_url: '',
      description: ''
    }

    if (this.voucherId != '') {
      this.activeMode = this.MODE_UPDATE;
      this.getVoucherById(this.voucherId);
      return true;
    }

    this.activeMode = this.MODE_CREATE;
  }

  getCustomers(name = '') {
    this.showLoading = true;
    this.customerService.getUsers({ name: name }).subscribe((res: any) => {
      this.customers = res.data.list;
      this.showLoading = false;
    }, err => {
      console.log(err);
    });
  }

  getPromo(name = '') {
    this.showLoading = true;
    this.promoService.getPromo({ name: name, status: this.PROMO_VOUCHER }).subscribe((res: any) => {
      this.promo = res.data.list;
      this.showLoading = false;
    }, err => {
      console.log(err);
    });
  }

  setSelectedPromo($event) {
    this.formModel.nominal_rupiah = $event.nominal_rupiah;
    this.formModel.photo_url = $event.photo_url;
    this.formModel.photo = $event.photo;
  }

  getVoucherById(voucherId) {
    this.voucherService.getVoucherById(voucherId).subscribe((res: any) => {
      this.formModel = res.data;
      // this.setSelectedPromo(this.formModel);
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
    this.voucherService.createVoucher(this.formModel).subscribe((res: any) => {
      this.landaService.alertSuccess('Berhasil', res.message);
      this.afterSave.emit();
    }, err => {
      this.landaService.alertError('Mohon Maaf', err.error.errors);
    });
  }

  update() {
    this.voucherService.updateVoucher(this.formModel).subscribe((res: any) => {
      this.landaService.alertSuccess('Berhasil', res.message);
      this.afterSave.emit();
    }, err => {
      this.landaService.alertError('Mohon Maaf', err.error.errors);
    });
  }

}
