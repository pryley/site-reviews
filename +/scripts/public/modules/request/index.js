/**
 * @site-reviews/request: the requests Site Reviews makes, and GLSR.Request.
 */
import { attach } from '@/public/core/index.js';
import Request from './request.js';

window.GLSR.Request = Request;

attach('request', () => {})

export default Request
