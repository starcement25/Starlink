import { StyleSheet } from 'react-native'
export default StyleSheet.create({
    container: { flex: 1, },
    _bgColor: { backgroundColor: '#ee1d23', flex: 1 },
    _upperView: {
        height: 150, alignItems: 'center', position: 'relative', justifyContent: 'center',
        _back_btn: {
            position: 'absolute', top: 10, right: 10,
            _img: { height: 30, width: 30, }
        },
        _txt_view: { flexDirection: 'row', width: '90%', alignItems: 'center', justifyContent: 'space-between' },
        _txt_big: { fontSize: 20, color: '#fff', fontWeight: '600' },
        _txt_small: { fontSize: 10, color: '#fff', fontWeight: '400' }
    },
    _user_profile: {
        width: '100%', justifyContent: 'center', alignItems: 'center',
        _view: {
            height: 120, width: 120, backgroundColor: 'blue', borderRadius: 60, position: 'relative',
            _img: { height: 120, width: 120, borderColor: '#ee1d23', borderRadius: 60, borderWidth: 2 },
            _camera: {
                height: 35, width: 35, backgroundColor: '#FFF0F1', borderRadius: 50, position: 'absolute', bottom: 0, right: 0, justifyContent: 'center', alignItems: 'center',
                _img: { height: 18, width: 18, }
            }
        }
    },
    _lowerView: {
        backgroundColor: '#fff', flex: 1, borderTopRightRadius: 100, position: 'relative',
        _scroll: {
            flex: 1, marginTop: '5%', borderTopRightRadius: 100, marginBottom: 10,
            _view: {
                width: '100%', alignItems: 'center',
                _txt_input_view: {
                    marginTop: '5%', width: '90%',
                    _view: {
                        width: '100%',
                        _txt: { fontWeight: '500', fontSize: 14, height: 40, paddingLeft: 0, color: '#000' },
                        _floating_txt: { fontWeight: '400', fontSize: 8, color: 'gray', marginBottom: -10 },
                        _icon: { height: 18, width: 18, position: 'absolute', bottom: 5, right: 0 },
                        _under_line: { borderBottomColor: 'gray', borderTopColor: 'transparent', borderLeftColor: 'transparent', borderRightColor: 'transparent', borderWidth: 1, marginTop: -10 }
                    }
                }
            }
        }
    },
    _btn: {
        backgroundColor: '#ee1d23', height: 45, width: '80%', borderRadius: 25, marginTop: '10%', justifyContent: 'center', alignItems: 'center',
        _txt: { color: '#fff', fontWeight: '600' }
    }
})
