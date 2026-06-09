import { StyleSheet } from 'react-native'

export default StyleSheet.create({
    container: { flex: 1, backgroundColor: '#ee1d23' },
    _bgColor: { backgroundColor: '#ee1d23', flex: 1 },
    _upperView: {
        justifyContent: 'center', alignItems: 'center', position: 'relative',
        _verification: { height: 70, width: 70 },
        _txt: { color: '#fff', fontSize: 16, fontWeight: '700' },
        _back: {
            position: 'absolute', top: 10, right: 10,
            _img: { height: 30, width: 30, }
        }
    },
    _lowerView: {
        backgroundColor: '#fff', flex: 1, borderTopRightRadius: 100, position: 'relative', padding: 16,
        _txt_view: {
            marginTop: 25, marginLeft: 10,
            _mobileTxt: { color: '#000', fontSize: 15, fontWeight: '600' },
            _enter_code: { color: '#000', fontSize: 15, fontWeight: '600', marginTop: 20 },
            _txt_up: { color: '#000', fontSize: 12, fontWeight: '400', marginTop: 5 },
            _txt_down: { color: '#000', fontSize: 12, fontWeight: '400' }
        },
        _loginBtn: {
            backgroundColor: '#ee1d23', height: 50, width: '100%', borderRadius: 10, marginTop: '5%', justifyContent: 'center', alignItems: 'center',
            _txt: { color: '#fff', fontWeight: '600', fontSize: 18 }
        }
    },
    bg: {
        height: '70%', width: '100%', justifyContent: 'center', alignItems: 'center', position: 'absolute', bottom: '0%',
        _img: { height: '85%', width: '85%', resizeMode: 'cover' }
    },
    _btn_section: {
        justifyContent: 'center', alignItems: 'center', marginTop: '5%',
        _txt: { color: '#000', fontSize: 12, fontWeight: '400' },
        _resend_btn: { color: '#EE1D23', fontSize: 16, fontWeight: '600', marginTop: 5, textDecorationStyle: 'solid', textDecorationColor: '#000', }
    },
    _otp_input_view: {
        width: '70%', height: 50, marginTop: '2%', flexDirection: 'row', alignItems: 'center', justifyContent: 'center',
        _otp_input: { borderRadius: 5, textAlign: 'center', fontSize: 18, fontWeight: '600', width: 45, height: 45, margin: 10, backgroundColor: '#FFECEC', color: '#000' },
        _otp_input_focus: { borderColor: '#EE1D23', borderWidth: 1, borderRadius: 5, textAlign: 'center', fontSize: 18, fontWeight: '600', width: 45, height: 45, backgroundColor: '#fff', color: '#000' }
    }
})
