import { StyleSheet } from 'react-native'

export default StyleSheet.create({
    container: { flex: 1 },

    _address_view: { flex: 1, backgroundColor: '#00000050', justifyContent: 'center', alignItems: 'center', position: 'relative', },

    _view: {
        height: '70%', width: '80%', backgroundColor: '#fff', borderRadius: 10, padding: 10, padding: 16, alignItems: 'center', position: 'relative',
        _header_txt: { fontWeight: '700', fontSize: 14, textAlign: 'center', color: '#000' },
        _text_input: { width: '100%', padding: 10, borderColor: 'gray', borderWidth: 1, borderRadius: 5, marginTop: 10, color: '#000' },
        _checkbox_txt: {
            flexDirection: 'row', marginTop: 10, alignItems: 'center', width: '100%', justifyContent: 'space-between',
            _txt: { fontSize: 12, fontWeight: '600', color: '#000' }
        },
        _btn: {
            backgroundColor: '#ee1d23', height: 35, width: '50%', borderRadius: 5, justifyContent: 'center', alignItems: 'center', marginTop: 10,
            _txt: { color: '#fff', fontWeight: '600' }
        },
        _close_btn: { height: 35, width: 35, justifyContent: 'center', alignItems: 'center', position: 'absolute', right: 0, top: 0, backgroundColor: '#fff', borderRadius: 5, }
    },

    _btn_section: {
        justifyContent: 'center', alignItems: 'center', marginTop: '2%', marginBottom: '2%', flexDirection: 'row',
        _txt: { color: '#000', fontSize: 12, fontWeight: '400' },
        _resend_btn: { color: '#000', fontSize: 15, fontWeight: '600', marginTop: -2, marginLeft: 5, textDecorationLine: 'underline', textDecorationStyle: 'solid', textDecorationColor: '#000', }
    }
})
