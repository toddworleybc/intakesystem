import moment from 'moment';

export default function dateFormater(theDate) {
    return moment(theDate).format('MMMM Do YYYY');
 }