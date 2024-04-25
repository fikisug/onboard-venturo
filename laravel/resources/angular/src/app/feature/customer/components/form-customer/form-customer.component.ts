import { Component, EventEmitter, Input, Output, SimpleChange } from '@angular/core';
import { CustomerService } from '../../services/customer.service';
import { LandaService } from 'src/app/core/services/landa.service';

@Component({
  selector: 'app-form-customer',
  templateUrl: './form-customer.component.html',
  styleUrls: ['./form-customer.component.scss']
})
export class FormCustomerComponent {
  readonly MODE_CREATE = 'add';
  readonly MODE_UPDATE = 'update';

  @Input() userId: string;
  @Output() afterSave = new EventEmitter<boolean>();

  activeMode: string;
  verified: any;

  formModel: {
    id: string,
    name: string,
    email: string,
    photo: string,
    photo_url: string,
    date_of_birth: string,
    phone_number: string,
    is_verified: string
  }

  constructor(
    private userService: CustomerService,
    private landaService: LandaService,
    // private progressService: ProgressServiceService,
    // public ngProgress: NgProgress,
  ) {
    // this.progressRef = this.ngProgress.ref();
  }

  // progressRef: NgProgressRef;

  ngOnInit(): void { }

  isDisabledForm: boolean = false;
  isLoading: boolean = false;


  getCroppedImage($event) {
    this.formModel.photo = $event;
  }



  getUser(userId) {

    this.userService.getUserById(userId).subscribe((res: any) => {
      this.formModel = res.data;
    }, err => {
      console.log(err);
    });
  }

  getRoles() {
      this.verified = [
    { "id": "0", "name": "Belum Verifikasi", "status": "Belum Verifikasi" },
    { "id": "1", "name": "Verifikasi", "status": "Verifikasi" }
  ];
  }

  resetForm() {
    this.getRoles();
    this.formModel = {
      id: '',
      name: '',
      email: '',
      photo: '',
      photo_url: '',
      date_of_birth: '',
      phone_number: '',
      is_verified: ''
    }

    if (this.userId != '') {
      this.activeMode = this.MODE_UPDATE;
      this.getUser(this.userId);
      return true;
    }

    this.activeMode = this.MODE_CREATE;
  }

  save() {
    switch (this.activeMode) {
      case this.MODE_CREATE:
        this.isDisabledForm = true;
        this.isLoading = true;
        this.insert();
        break;
      case this.MODE_UPDATE:
        this.isDisabledForm = true;
        this.isLoading = true;
        this.update();
        break;
    }
  }

  insert() {
    this.userService.createUser(this.formModel).subscribe((res: any) => {
      this.landaService.alertSuccess('Berhasil', res.message);
      this.afterSave.emit();
      this.isDisabledForm = false;
      this.isLoading = false;
    }, err => {
      this.landaService.alertError('Mohon Maaf', err.error.errors);
      this.isDisabledForm = false;
      this.isLoading = false;
    });
  }


  update() {
    this.userService.updateUser(this.formModel).subscribe((res: any) => {
      this.landaService.alertSuccess('Berhasil', res.message);
      this.afterSave.emit();
      this.isDisabledForm = false;
      this.isLoading = false;
    }, err => {
      this.landaService.alertError('Mohon Maaf', err.error.errors);
      this.isDisabledForm = false;
      this.isLoading = false;
    });
  }


  ngOnChanges(changes: SimpleChange) {
    this.resetForm();
  }
}
