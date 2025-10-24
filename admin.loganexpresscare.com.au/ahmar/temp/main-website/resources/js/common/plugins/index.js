import moment from "moment";
import './axiosBase';
import './axiosAdmin';
import './axiosFront';
import './axiosPdf';

moment.suppressDeprecationWarnings = true;
window.moment = moment;
