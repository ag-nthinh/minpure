function disp(){
    // 「OK」時の処理開始 ＋ 確認ダイアログの表示
    if(window.confirm('本当に良いですか？')){
        return true;

    } else{
        return false;
    }
    // 「キャンセル」時の処理終了
}
