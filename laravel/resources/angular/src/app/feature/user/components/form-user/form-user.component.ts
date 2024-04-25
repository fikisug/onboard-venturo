import { Component, EventEmitter, Input, Output, SimpleChange } from '@angular/core';
import { UserService } from '../../services/user.service';
import { LandaService } from 'src/app/core/services/landa.service';
import { ProgressServiceService } from 'src/app/feature/core/progress-service.service';
import { NgProgress, NgProgressRef } from 'ngx-progressbar';

@Component({
  selector: 'app-form-user',
  templateUrl: './form-user.component.html',
  styleUrls: ['./form-user.component.scss']
})
export class FormUserComponent {
  cities = ["Malang", "Surabaya", "Mojokerto"];

  readonly MODE_CREATE = 'add';
  readonly MODE_UPDATE = 'update';

  @Input() userId: string;
  @Output() afterSave = new EventEmitter<boolean>();

  activeMode: string;
  roles: any;

  formModel: {
    id: string,
    name: string,
    email: string,
    photo: string,
    photo_url: string,
    password: string,
    phone_number: string,
    user_roles_id: string
  }

  constructor(
    private userService: UserService,
    private landaService: LandaService,
    private progressService: ProgressServiceService,
    public ngProgress: NgProgress,
  ) {
    this.progressRef = this.ngProgress.ref();
  }

  progressRef: NgProgressRef;

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
    this.userService.getRoles().subscribe((res: any) => {
      this.roles = res.data.list;
    }, err => {
      console.log(err);
    });
  }

  resetForm() {
    this.getRoles();
    this.formModel = {
      id: '',
      name: '',
      email: '',
      photo: '',
      photo_url: '',
      password: '',
      phone_number: '',
      user_roles_id: ''
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
